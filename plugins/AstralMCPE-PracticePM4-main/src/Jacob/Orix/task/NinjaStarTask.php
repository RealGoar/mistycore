<?php


namespace Jacob\Orix\task;


use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class NinjaStarTask extends Task
{
    public function __construct(
        public Player $player,
        public Player $teleport,
        public $time = 3,
    ){}

    public function onRun(): void
    {
        if ($this->time !== 0) {
            --$this->time;
        }
        if ($this->player === null || !$this->player->isOnline() || $this->player->isClosed()) {
            $this->getHandler()->cancel();
            return;
        }
        if ($this->teleport === null || !$this->teleport->isOnline() || $this->teleport->isClosed()) {
            $this->getHandler()->cancel();
            return;
        }
        if ($this->time <= 7 && $this->time >= 1) {
            $message = "§dThe player §d§l{$this->player->getName()} §r§dwill teleport to you in §d§l{$this->time} §r§dseconds.";
            $this->teleport->sendMessage($message);
        }
        if($this->time <= 0){
            $this->player->teleport($this->teleport->getLocation());
            $this->getHandler()->cancel();
        }
    }
}