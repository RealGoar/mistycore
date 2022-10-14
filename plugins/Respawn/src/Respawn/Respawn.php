<?php


namespace Respawn;

use Respawn\Utils\Storage;
use Respawn\Utils\Scheduler;
use Respawn\Utils\Utils;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;

use \Closure;

final class Respawn {

    private $player;
    private $duration;
    private $scheduler = null;
    private $closureOnResolve = null;
    private $closureOnReject = null;

    private function __construct(Player $player, int $duration) {
        $this->player = $player;
        $this->duration = $duration;
    }

    public static function create(Player $player, int $delay = 0): Respawn {
        return new Self($player, $delay);
    }

    public function then(Closure $onResolve, Closure $onReject): Respawn {
        $this->closureOnResolve = $onResolve;
        $this->closureOnReject = $onReject;
        return $this;
    }

    public function run(): Respawn {
        if ($this->duration > 0 && $this->scheduler === null) {
            $this->scheduler = new Scheduler($this);
            return $this;
        }

        $position = Storage::getInstance()->getRandomPosition();
        if (!$position instanceof Position) {
            $this->cancel();
            return $this;
        }

        $this->resolve($position);
        return $this;
    }

    public function baseTick() {
        if ($this->duration === 0) {
            $position = Storage::getInstance()->getRandomPosition();
            if (!$position instanceof Position) {
                $this->cancel();
                return;
            }

            $this->resolve($position);
            return;
        }

        $this->duration--;
    }

    private function resolve(Position $position): Respawn {
        if ($this->scheduler !== null) {
            $this->scheduler->cancel();
        }
        $this->scheduler = null;
        $world = $position->getWorld();
        $closure = $this->closureOnResolve;
        $world->loadChunk($position->getX(), $position->getZ());
        $this->player->teleport($position);
        ($closure)($this->player);
        return $this;
    }

    public function cancel(): Respawn {
        if ($this->scheduler !== null) {
            $this->scheduler->cancel();
        }
        $this->scheduler = null;
        $closure = $this->closureOnReject;
        ($closure)($this->player);
        return $this;
    }
}