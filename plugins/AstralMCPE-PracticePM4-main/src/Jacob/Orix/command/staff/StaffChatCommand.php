<?php

namespace Jacob\Orix\command\staff;

use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class StaffChatCommand extends Command {
	
	/**
     * StaffChatCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("sc.command.use");
        parent::__construct("sc", "Send messages to other staffs");
        
    }
	
	/**
     * @param CommandSender $sender
     * @param String $label
     * @param array $args
     * @return void
     */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }
		if(!$this->testPermission($sender)){
			return;
        }
        if(empty($args)){
        	$sender->sendMessage(TE::LIGHT_PURPLE."You must write a message, it cannot be empty");
        	return;
        }
        foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player){
        	if($player->hasPermission("sc.command.use")){
        		$player->sendMessage(TE::BLUE."[StaffChat]".TE::RESET." ".TE::DARK_AQUA."[".$sender->getWorld()->getFolderName()."]".TE::RESET." ".TE::AQUA.$sender->getName().TE::GRAY.": ".TE::YELLOW.implode(" ", $args));
        	}
        }
    }
}