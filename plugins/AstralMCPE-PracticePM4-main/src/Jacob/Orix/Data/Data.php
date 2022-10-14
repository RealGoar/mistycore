<?php

namespace Jacob\Orix\Data;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\task\asynctask\DiscordMessage;
use Jacob\Orix\util\Translator;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\block\Block;

class Data {
	
	/**
	 * @param String $playerName
	 * @param String $senderName
	 * @param String $reason
	 * @param bool $isPermanent
	 * @param mixed $dataTime
	 * @param String $address
	 * @param String $uuid
	 */
	public static function addBan(String $playerName, String $senderName, String $reason, bool $isPermanent = false, $time = null, ?String $address = null, ?String $uuid = null){
		$elapsed = microtime(true);
		$date = date("d/m/y H:i:s");
		if($isPermanent){
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
			$file->set($playerName, ["sender_name" => $senderName, "reason_of_ban" => $reason, "address" => $address, "uuid" => $uuid, "date" => $date, "elapsed" => $elapsed]);
			$file->save();
		}else{
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timebanneds.yml", Config::YAML);
			$file->set($playerName, ["sender_name" => $senderName, "reason_of_ban" => $reason, "address" => $address, "uuid" => $uuid, "time_ban" => $time, "date" => $date, "elapsed" => $elapsed]);
			$file->save();
		}
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $embed->setDescription("Player Banned: $senderName\nReason: $reason\nDate: $date\nPermanent (1 = true, nothing = false): $isPermanent\nTime Banned For: $time");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
	
	/**
	 * @param String $playerName
	 * @param bool $isPermanent
	 */
	public static function removeBan(String $playerName, bool $isPermanent = false){
		if($isPermanent){
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
			$file->remove($playerName);
			$file->save();
		}else{
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timebanneds.yml", Config::YAML);
			$file->remove($playerName);
			$file->save();
		}
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $embed->setDescription("Player Unbanned: $playerName");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
	
	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isPermanentlyBanned(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_banneds.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}

	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isTemporarilyBanned(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timebanneds.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param String $playerName
	 * @param String $senderName
	 * @param String $reason
	 * @param bool $isPermanent
	 * @param mixed $time
	 */
	public static function addMute(String $playerName, String $senderName, String $reason, bool $isPermanent = false, $time = null){
		$elapsed = microtime(true);
		$date = date("d/m/y H:i:s");
		if($isPermanent){
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_muteds.yml", Config::YAML);
			$file->set($playerName, ["sender_name" => $senderName, "reason_of_mute" => $reason, "date" => $date, "elapsed" => $elapsed]);
			$file->save();
		}else{
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timemuteds.yml", Config::YAML);
			$file->set($playerName, ["sender_name" => $senderName, "reason_of_mute" => $reason, "time_mute" => $time, "date" => $date, "elapsed" => $elapsed]);
			$file->save();
		}
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $embed->setDescription("Player Muted: $senderName\nReason: $reason\nDate: $date\nTime Muted For: $time");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
	
	/**
	 * @param String $playerName
	 * @param bool $isPermanent
	 */
	public static function removeMute(String $playerName, bool $isPermanent = false){
		if($isPermanent){
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_muteds.yml", Config::YAML);
			$file->remove($playerName);
			$file->save();
		}else{
			$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timemuteds.yml", Config::YAML);
			$file->remove($playerName);
			$file->save();
		}
	}
	
	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isPermanentlyMuted(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_muteds.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}

	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isTemporarilyMuted(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_timemuteds.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param String $playerName
	 * @param String $senderName
	 * @param String $reason
	 */
	public static function addWarn(String $playerName, String $senderName, String $reason){
		$elapsed = microtime(true);
		$date = date("d/m/y H:i:s");
		
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."warns.yml", Config::YAML);
		$warns = count($file->get($playerName, []));
		$result = $file->get($playerName, []);
		
		$index = empty($warns) ? 0 : $warns++;
			
		$result[$index] = ["sender_name" => $senderName, "reason_of_warn" => $reason, "date" => $date, "elapsed" => $elapsed, "warn_id" => $index];
		$file->set($playerName, $result);
		$file->save();
		$webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
		$embed = new Embed();
		$msg = new Message();
		$embed->setTitle("Punishment Logs");
		$embed->setDescription("Player Warned: $senderName\nReason: $reason\nDate: $date\nCurrent Warns: $index");
		$msg->addEmbed($embed);
		$webhook->send($msg);
	}
	
	/**
	 * @param String $playerName
	 * @param Int $warnID
	 */
	public static function removeWarn(String $playerName, ?Int $warnID = 0){
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."warns.yml", Config::YAML);
		
		$warns = count($file->get($playerName));
		$result = $file->get($playerName);
		unset($result[$warns - 1]);
		if($warns === 1){
			$file->remove($playerName);
			$file->save();
			return;
		}
		$file->set($playerName, $result);
		$file->save();
	}
	
	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isWarned(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."warns.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param String $playerName
	 * @param String $command
	 */
	public static function addCommandLog(String $playerName, String $command){
		$elapsed = microtime(true);
		$date = date("d/m/y H:i:s");
		
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_commands.yml", Config::YAML);
		$commands = count($file->get($playerName, []));
		$result = $file->get($playerName, []);
		
		$index = empty($commands) ? 0 : $commands++;
			
		$result[$index] = ["player_name" => $playerName, "command_execute" => $command, "date" => $date, "elapsed" => $elapsed];
		$file->set($playerName, $result);
		$file->save();
	}
	
	/**
	 * @param String $playerName
	 * @param Int $warnID
	 */
	public static function removeCommandLog(String $playerName){
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_commands.yml", Config::YAML);
		
		$commands = count($file->get($playerName));
		$result = $file->get($playerName);
		unset($result[$commands - 1]);
		if($commands === 1){
			$file->remove($playerName);
			$file->save();
			return;
		}
		$file->set($playerName, $result);
		$file->save();
	}
	
	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isCommandLog(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_commands.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param String $playerName
	 * @param String $damagerName
	 * @param String $cause
	 * @param String $itemName
	 * @param String $playerCoordinates
	 */
	public static function addDeathLog(String $playerName, ?String $damagerName, ?String $cause, ?String $itemName, ?Vector3 $position){
		if(self::isDeathLog($playerName)) self::removeDeathLog($playerName);
		
		$elapsed = microtime(true);
		$date = date("d/m/y H:i:s");
		
		$playerCoordinates = Translator::vector3ToString($position);
		
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_deaths.yml", Config::YAML);
		$file->set($playerName, ["damager_name" => is_null($damagerName) ? "nothing" : $damagerName, "cause" => $cause, "item_name" => is_null($itemName) ? "nothing" : $itemName, "player_coordinates" => $playerCoordinates, "date" => $date, "elapsed" => $elapsed]);
		$file->save();
	}
	
	/**
	 * @param String $playerName
	 */
	public static function removeDeathLog(String $playerName){
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_deaths.yml", Config::YAML);
		$file->remove($playerName);
		$file->save();
	}
	
	/**
	 * @param String $playerName
	 * @return bool
	 */
	public static function isDeathLog(String $playerName) : bool {
		$file = new Config(AdvancedPractice::getInstance()->getDataFolder()."players_deaths.yml", Config::YAML);
		if($file->exists($playerName)){
			return true;
		}
		return false;
	}
	
	/**
	 * @param String $file
	 * @return Config
	 */
	public static function getData(String $file){
		return new Config(AdvancedPractice::getInstance()->getDataFolder()."{$file}.yml", Config::YAML);
	}
}

?>