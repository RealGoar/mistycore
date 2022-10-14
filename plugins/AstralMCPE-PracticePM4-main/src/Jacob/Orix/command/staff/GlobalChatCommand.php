<?php

namespace Jacob\Orix\command\staff;

use pocketmine\permission\DefaultPermissions;
use Jacob\Orix\AdvancedPractice;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class GlobalChatCommand extends Command {

    /**
     * GlobalChatCommand Constructor.
     */
    public function __construct(){
        parent::__construct("gchat");
    }

    /**
     * @param CommandSender $sender
     * @param String $label
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, String $label, Array $args) : void {
        if(!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
            return;
        }
        if(AdvancedPractice::getInstance()->globalChat){
        	AdvancedPractice::getInstance()->globalChat = false;
        	AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::GREEN."Global chat was unmuted");
        }else{
        	AdvancedPractice::getInstance()->globalChat = true;
        	AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::GREEN."Global chat was muted");
        }
    }
}

?>