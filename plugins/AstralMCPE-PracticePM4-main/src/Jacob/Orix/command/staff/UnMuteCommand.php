<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class UnMuteCommand extends Command {
	
	/**
	 * UnMuteCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("unmute.command.use");
		parent::__construct("unmute");
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
		if(Data::isPermanentlyMuted($args[0])){
			Data::removeMute($args[0], true);
			AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$args[0].TE::RESET.TE::GRAY." was unmuted from the network by ".TE::BOLD.TE::AQUA.$sender->getName());
		}elseif(Data::isTemporarilyMuted($args[0])){
			Data::removeMute($args[0], false);
			AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$args[0].TE::RESET.TE::GRAY." was unmuted from the network by ".TE::BOLD.TE::AQUA.$sender->getName());
		}else{
			$sender->sendMessage(TE::LIGHT_PURPLE.$args[0]." was not muted from the network!");
		}
	}
}

?>