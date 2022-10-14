<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\PlayerBase;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\{Command, CommandSender};

class CheckDeviceCommand extends Command {
	
	/**
	 * CheckDeviceCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("check.command.use");
		parent::__construct("device");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(!$this->testPermission($sender)){
			return;
        }
        if(empty($args)){
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName]");
			return;
		}
        $player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[0]);
		if(!$player instanceof Player){
			$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
			return;
		}
		$sender->sendMessage(TE::GRAY."The player ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." is playing from the device ".TE::LIGHT_PURPLE.PlayerBase::getDevice($player));
	}
}