<?php

namespace Jacob\Orix\Data;

use pocketmine\block\VanillaBlocks;
use pocketmine\player\GameMode;
use Jacob\Orix\AdvancedPractice;

use Jacob\Orix\anticheat\Alerts;

use pocketmine\player\Player;
use pocketmine\item\{Item, ItemIds, VanillaItems};
use pocketmine\utils\{Config, TextFormat as TE};

use pocketmine\network\mcpe\protocol\LoginPacket;

class PlayerBase {
	
	/** @var array[] */
	protected static array $players = [];
	
	/** @var array[] */
	protected static array $freeze = [];
	
	/** @var array[] */
	protected static array $inspector = [];
	
	/** @var array[] */
	protected static array $device = [];
	
	/** @var array[] */
	protected static array $staffs = [], $inventory = [];
	
	/**
	 * @param String $playerName
	 * @param String $address
	 * @param String $xuid
	 * @param String $uuid
	 * @param String $device
	 * @return void
	 */
	public static function create(String $playerName, String $address, String $xuid, String $uuid, String $device) : void {
		$config = new Config(AdvancedPractice::getInstance()->getDataFolder()."players.yml", Config::YAML);
		$config->set($playerName, ["address" => $address, "xuid" => $xuid, "uuid" => $uuid, "device" => $device]);
		$config->save();
	}
	
	/**
	 * @param String $playerName
	 * @return void
	 */
	public static function remove(String $playerName) : void {
		$config = new Config(AdvancedPractice::getInstance()->getDataFolder()."players.yml", Config::YAML);
		if($config->exists($playerName)){
			$config->remove($playerName);
			$config->save();
		} //finished
	}

	/**
	 * @param Player
	 * This function is called in order to better use the PMMP's hidePlayer and showPlayer methods.
	 */
	public static function hidePlayer(Player $player){
		self::$players[$player->getName()] = $player;
	}

	/**
	 * @param Player
	 * This function is called in order to better use the PMMP's hidePlayer and showPlayer methods.
	 */
	public static function showPlayer(Player $player){
		unset(self::$players[$player->getName()]);
	}

	/**
	 * @return array[]
	 */
	public static function getPlayersHide() : Array {
		return self::$players;
	}

	/**
	 * @return bool
	 */
	public static function isEmptyPlayers() : bool {
		if(empty(self::$players)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param Player $player
	 * @return void
	 */
	public static function addStaff(Player $player) : void {
		self::$staffs[$player->getName()] = $player;
		
		foreach(self::getStaffs() as $online){
             $online->sendMessage(TE::BLUE."[Staff]".TE::RESET." ".TE::DARK_AQUA."[".$player->getWorld()->getDisplayName()."]".TE::RESET." ".TE::AQUA.$player->getName().TE::GRAY.": ".TE::GREEN."active staff mode!");
        }
        
        self::hidePlayer($player);
        foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
        	if(!$online->hasPermission("mod.command.use")){
        		$online->hidePlayer($player);
        	}
        }
        
        if(!Alerts::isEnable($player)) Alerts::setEnable($player);
		
		self::$inventory[$player->getName()]["inventory"] = $player->getInventory()->getContents();
		self::$inventory[$player->getName()]["armor"] = $player->getArmorInventory()->getContents();
		
		$player->setGamemode(GameMode::CREATIVE());
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		
		$player->getInventory()->setItem(0, VanillaBlocks::PACKED_ICE()->asItem()->setCustomName(TE::AQUA."Freeze"));
        $player->getInventory()->setItem(1, VanillaItems::COMPASS()->setCustomName(TE::YELLOW."Teleporter"));
        $player->getInventory()->setItem(2, VanillaItems::CLOCK()->setCustomName(TE::AQUA."Random player"));
        //$player->getInventory()->setItem(6, Item::get(ItemIds::BOOK, 1, 1)->setCustomName(TE::GREEN."Information"));
        $player->getInventory()->setItem(7, VanillaItems::RED_DYE()->setCustomName(TE::LIGHT_PURPLE."Disable Vanish"));
	}
	
	/**
	 * @param Player $player
	 * @return void
	 */
	public static function removeStaff(Player $player) : void {
		if(!self::isStaff($player)) return;
		
		foreach(self::getStaffs() as $online){
             $online->sendMessage(TE::BLUE."[Staff]".TE::RESET." ".TE::DARK_AQUA."[".$player->getWorld()->getDisplayName()."]".TE::RESET." ".TE::AQUA.$player->getName().TE::GRAY.": ".TE::LIGHT_PURPLE."desactive staff mode!");
        }
        self::showPlayer($player);
        foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
        	if(!$online->hasPermission("mod.command.use")){
        		$online->showPlayer($player);
        	}
        }
        
        if(Alerts::isEnable($player)) Alerts::setDisable($player);
        
		unset(self::$staffs[$player->getName()]);
		
		$player->setGamemode(GameMode::SURVIVAL());
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		
		if(isset(self::$inventory[$player->getName()]["inventory"])) $player->getInventory()->setContents(self::$inventory[$player->getName()]["inventory"]);
		if(isset(self::$inventory[$player->getName()]["armor"])) $player->getArmorInventory()->setContents(self::$inventory[$player->getName()]["armor"]);
		
		unset(self::$inventory[$player->getName()]["inventory"]);
		unset(self::$inventory[$player->getName()]["armor"]);
	}
	
	/**
	 * @param Player $player
	 * @return bool
	 */
	public static function isStaff(Player $player) : bool {
		if(isset(self::$staffs[$player->getName()])){
			return true;
		}
		return false;
	}
	
	/**
	 * @return array[]
	 */
	public static function getStaffs() : Array {
		return self::$staffs;
	}
	
	/**
	 * @param Player $player
	 * @return void
	 */
	public static function addFreeze(Player $player) : void {
		self::$freeze[$player->getName()] = $player;
	}
	
	/**
	 * @param Player $player
	 * @return void
	 */
	public static function removeFreeze(Player $player) : void {
		unset(self::$freeze[$player->getName()]);
	}
	
	/**
	 * @param Player $player
	 * @return bool
	 */
	public static function isFreeze(Player $player) : bool {
		if(isset(self::$freeze[$player->getName()])){
			return true;
		}
		return false;
	}
	
	/**
	 * @param LoginPacket $packet
	 */
	public static function addDevice(LoginPacket $packet) : void {
		self::$device[$packet->username] = $packet->clientData["DeviceOS"];
	}
	
	/**
	 * @param Player $player
	 * @return String
	 */
	public static function getDevice(Player $player) : ?String {
		if(!isset(self::$device[$player->getName()])) {
            return null;
        }
		$device = self::$device[$player->getName()];
		if(is_int($device)) {
            return self::getDeviceIntToString($device);
        }
        return null;
	}
	
	/**
	 * @param Int $device
	 * @return String
	 */
	public static function getDeviceIntToString(Int $device) : String {
		if($device === 1){
            $d = "Android";
        }elseif($device === 2){
            $d = "iOS";
        }elseif($device === 3){
            $d = "Mac";
        }elseif($device === 4){
            $d = "FireIOS";
        }elseif($device === 5){
            $d = "GearVR";
        }elseif($device === 6){
            $d = "Hololens";
        }elseif($device === 7){
            $d = "Windows_10";
        }elseif($device === 8){
            $d = "Windows_7";
        }elseif($device === 9){
            $d = "NoName";
        }elseif($device === 10){
            $d = "PlayStation_4";
        }else{
            $d = "Not_Registered";
        }
        return $d;
	}
}

?>