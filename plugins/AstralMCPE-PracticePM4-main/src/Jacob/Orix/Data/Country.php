<?php

namespace Jacob\Orix\Data;

use pocketmine\player\Player;

class Country {
	
	/** 
	 * @param Player $player
	 * @return String
	 */
	public static function getCountry(Player $player) : String {
		$ip = $player->getNetworkSession()->getIp();
		$http = file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip);
		$handle = json_decode($http);
		return is_null($handle->geoplugin_countryName) ? "Unknown" : $handle->geoplugin_countryName;
	}
}