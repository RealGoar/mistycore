<?php namespace Jacob\Orix\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use pocketmine\utils\TextFormat;

class SpawnCommand extends Command {

	public function __construct() {
		parent::__construct("spawn", "Warp back to spawn.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if ($sender instanceof Player) {
			$sender->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
			$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Teleported to spawn.");
			AdvancedPractice::getSessionManager()->getPlayerSession($sender)->giveHubKit();
		}
	}

}