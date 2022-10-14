<?php

namespace Jacob\Orix\command\staff;

use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;

use Jacob\Orix\Data\PlayerBase;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class StaffCommand extends Command {
	
	/**
     * StaffCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("mod.command.use");
        parent::__construct("mod");
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
            return;
        }
        if(PlayerBase::isStaff($sender)){
        	PlayerBase::removeStaff($sender);
      	  PlayerBase::showPlayer($sender);
    	    foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
     	       $online->showPlayer($sender);
     	   }	
        }else{
        	PlayerBase::addStaff($sender);
        	PlayerBase::hidePlayer($sender);
   	     foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
   			if(!$online->hasPermission("mod.command.use")){
     	    	   $online->hidePlayer($sender);
     	 	  }
    	    }
        }
    }
}