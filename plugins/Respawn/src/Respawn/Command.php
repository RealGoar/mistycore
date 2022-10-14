<?php

declare(strict_types = 1);

namespace Respawn;

use Respawn\Utils\Storage;
use Respawn\Utils\Utils;
use Respawn\Respawn;
use pocketmine\Server;
use pocketmine\command\Command as PMCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Command extends PMCommand {

    public function __construct() {
        parent::__construct('rffa', 'record spawn points ', '/rffa help', ['respawnffa']);
        Main::getInstance()->getServer()->getCommandMap()->register('/rffa', $this);
    }

    public function execute(CommandSender $player, string $label, array $args) {
        if (!$player instanceof Player) {
            $player->sendMessage("§l§4»§r§4 use the command in the game");
            return;
        }

        if (isset($args[0]) && strtolower($args[0]) === 'register') {
            if (!Server::getInstance()->isOp($player->getName())) {
                return;
            }
            Storage::getInstance()->register(Utils::positionToArray($player->getPosition()));
            $player->sendMessage("§l§e» §r§a point recorded successfully");
            return;
        } else if (isset($args[0]) && strtolower($args[0]) === 'respawn') {
            $delay = 0;
            if (isset($args[1])) {
                if (is_numeric($args[1]) && $args[1] >= 0) {
                    $delay = $args[1];
                }
            }
            Respawn::create($player, (int)$delay)
            ->then(
                function ($player) {
                    $player->sendMessage("§l§a» §r§awelcome :D");
                },
                function ($player) {
                    $player->sendMessage("§l§4» §r§4you couldn't respawn :(");
                },
            )->run();
            return;
        } else {
            $this->sendHelp($player, $label);
            return;
        }

        $this->sendHelp($player, $label);
    }

    public function sendHelp(Player $player, string $label) {
        $player->sendMessage(" ");
        $player->sendMessage("§7----- Respawn Command §r§7-----");
        $player->sendMessage("§7- §6/" . $label . " register §erecord a point");
        $player->sendMessage("§7- §6/" . $label . " respawn (optional|delay) §erandomly respawn ");
        $player->sendMessage(" ");
    }
}