<?php

namespace Jacob\Orix\command\staff; 

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Country;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\ {
	Command,
	CommandSender
};

class GipCommand extends Command {

	/**
	* GipCommand Constructor.
	*/
	public function __construct() {
		$this->setPermission("gip.command.use");
		parent::__construct("gip");
	}

	/**
	* @param CommandSender $sender
	* @param String $label
	* @param array $args
	* @return void
	*/
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if (!$this->testPermission($sender)) {
			return;
		}
		if (empty($args)) {
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName]");
			return;
		}
		$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[0]);
		if (!$player instanceof Player) {
			$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
			return;
		}
		$sender->sendMessage(TE::GRAY."The player ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." is playing from the country of ".TE::LIGHT_PURPLE . Country::getCountry($player));
	}
}