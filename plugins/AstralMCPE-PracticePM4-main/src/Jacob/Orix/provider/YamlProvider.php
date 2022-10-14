<?php

namespace Jacob\Orix\provider;

use Jacob\Orix\AdvancedPractice;

use pocketmine\utils\{Config, TextFormat as TE};
use pocketmine\Player;

class YamlProvider {
	
	/**
	 * @return void
	 */
	public static function init() : void {
		AdvancedPractice::getInstance()->saveResource("config.yml");
		if(!is_dir(AdvancedPractice::getInstance()->getDataFolder()."backups")){
			@mkdir(AdvancedPractice::getInstance()->getDataFolder()."backups");
		}
		if(!is_dir(AdvancedPractice::getInstance()->getDataFolder()."players")){
			@mkdir(AdvancedPractice::getInstance()->getDataFolder()."players");
		}
	}
}