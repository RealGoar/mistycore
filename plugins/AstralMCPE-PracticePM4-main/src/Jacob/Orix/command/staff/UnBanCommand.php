<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class UnBanCommand extends Command {
	
	/**
	 * UnBanCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("unban.command.use");
		parent::__construct("unban");
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
		if(Data::isPermanentlyBanned($args[0])){
			Data::removeBan($args[0], true);
			AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$args[0].TE::RESET.TE::GRAY." was unbanned from the network by ".TE::BOLD.TE::AQUA.$sender->getName());
		}elseif(Data::isTemporarilyBanned($args[0])){
			Data::removeBan($args[0], false);
			AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$args[0].TE::RESET.TE::GRAY." was unbanned from the network by ".TE::BOLD.TE::AQUA.$sender->getName());
		}else{
			$sender->sendMessage(TE::LIGHT_PURPLE.$args[0]." was not banned from the network!");
		}
	}
}

?>