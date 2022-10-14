<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\{Config, TextFormat as TE};

class BanListCommand extends Command {
	
	/**
	 * BanListCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("banlist.command.use");
		parent::__construct("banlist");
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
		$permanently = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
		
		$temporarily = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timebanneds.yml", Config::YAML);
	}
}

?>