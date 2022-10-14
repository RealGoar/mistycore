<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\PlayerBase;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class HelpCommand extends Command {
	
	/**
	 * HelpCommand Constructor.
	 */
	public function __construct(){
		parent::__construct("helpop", "Ask for help from staff members");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(empty($args)){
        	$sender->sendMessage(TE::LIGHT_PURPLE."You must write a message, it cannot be empty");
        	return;
        }
		$message = implode(" ", $args);
		foreach(PlayerBase::getStaffs() as $player){
			$player->sendMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::LIGHT_PURPLE.$sender->getName().TE::RESET.TE::GRAY." is requesting help from the staff".TE::AQUA.": ".$message);
		}
	}
}

?>