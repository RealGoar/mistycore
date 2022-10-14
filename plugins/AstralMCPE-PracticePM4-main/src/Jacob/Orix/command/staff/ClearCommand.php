<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\{Command, CommandSender};

class ClearCommand extends Command {
	
	/**
	 * ClearCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("clear.command.use");
		parent::__construct("clear", "Clear a player's inventory");
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
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in-game");
            return;
        }
        if(!empty($args[0])){
        	if($args[0] === "all"){
        		foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player){
        			$player->getInventory()->clearAll();
	    			$player->getArmorInventory()->clearAll();
					$player->getEffects()->clear();
        		}
        		$sender->sendMessage(TE::GRAY."You successfully emptied the inventory of: ".TE::AQUA.count(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers()).TE::GRAY." players");
        	}else{
	        	$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[0]);
	        	if(!$player instanceof Player){
	        		$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
	        		return;
	        	}
	        	$player->getInventory()->clearAll();
	    		$player->getArmorInventory()->clearAll();
				$player->getEffects()->clear();
				$sender->sendMessage(TE::GRAY."You successfully emptied the inventory of: ".TE::AQUA.$player->getName());
			}
 	   }else{
        	$sender->getArmorInventory()->clearAll();
			$sender->getInventory()->clearAll();
			$sender->getEffects()->clear();
			$sender->sendMessage(TE::GRAY."You successfully cleaned your inventory");
		}
	}
}