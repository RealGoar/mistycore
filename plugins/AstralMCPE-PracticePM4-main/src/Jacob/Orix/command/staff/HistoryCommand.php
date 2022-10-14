<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;
use Jacob\Orix\util\Time;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class HistoryCommand extends Command {
	
	/**
	 * HistoryCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("history.command.use");
		parent::__construct("history", "Can check the sanctions of the players");
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
		if(empty($args)){
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} help (view list of commands)");
			return;
		}
		switch($args[0]){
			case "cban":
				if(!$this->testPermission($sender)){
					$sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
					return;
				}
				if(empty($args[1])){
					$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} {$args[0]} [string: playerName]");
					return;
				}
				$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[1]);
				if($player instanceof Player){
					if(Data::isPermanentlyBanned($player->getName())){
						$file = Data::getData("players_banneds");
						$result = $file->get($player->getName());

						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." was banned from the network, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_ban"].TE::RESET." ".TE::GRAY."Banned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}elseif(Data::isTemporarilyBanned($player->getName())){
						$file = Data::getData("players_timebanneds");
						$result = $file->get($player->getName());

						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." was banned from the network, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_ban"].TE::RESET." ".TE::GRAY."Banned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Time Left: ".TE::WHITE.Time::getTimeLeft($result["time_ban"]).TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}else{
						$sender->sendMessage(TE::LIGHT_PURPLE.$player->getName()." he was not previously banned");
					}
				}else{
					if(Data::isPermanentlyBanned($args[1])){
						$file = Data::getData("players_banneds");
						$result = $file->get($args[1]);

						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$args[1].TE::GRAY." was banned from the network, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_ban"].TE::RESET." ".TE::GRAY."Banned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}elseif(Data::isTemporarilyBanned($args[1])){
						$file = Data::getData("players_timebanneds");
						$result = $file->get($args[1]);

						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$args[1].TE::GRAY." was banned from the network, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_ban"].TE::RESET." ".TE::GRAY."Banned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Time Left: ".TE::WHITE.Time::getTimeLeft($result["time_ban"]).TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}else{
						$sender->sendMessage(TE::LIGHT_PURPLE.$args[1]." he was not previously banned!");
					}
				}
			break;
			case "cdeath":
				if(!$this->testPermission($sender)){
					$sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
					return;
				}
				if(empty($args[1])){
					$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} {$args[0]} [string: playerName]");
					return;
				}
				$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[1]);
				if($player instanceof Player){
					if(!Data::isDeathLog($player->getName())){
						$sender->sendMessage(TE::LIGHT_PURPLE.$player->getName()." has no death records!");
						return;
					}
					$file = Data::getData("players_deaths");
					$result = $file->get($player->getName());
					
					$sender->sendMessage(AdvancedPractice::SYSTEM.TE::LIGHT_PURPLE.$player->getName().TE::GRAY." died on the date ".TE::BLUE."[".$result["date"].TE::BLUE."]"."\n".TE::GRAY."Damager".TE::WHITE.": ".TE::AQUA.$result["damager_name"]."\n".TE::GRAY."Cause".TE::WHITE.": ".TE::AQUA.$result["cause"]."\n".TE::GRAY."Item Name".TE::WHITE.": ".TE::AQUA.$result["item_name"]."\n".TE::GRAY."Coordinates".TE::WHITE.": ".TE::AQUA.$result["player_coordinates"]."\n".TE::GRAY."Elapsed".TE::WHITE.": ".TE::AQUA.Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
				}else{
					if(!Data::isDeathLog($args[1])){
						$sender->sendMessage(TE::LIGHT_PURPLE.$args[1]." has no death records!");
						return;
					}
					$file = Data::getData("players_deaths");
					$result = $file->get($args[1]);
					
					$sender->sendMessage(AdvancedPractice::SYSTEM.TE::LIGHT_PURPLE.$args[1].TE::GRAY." died on the date ".TE::BLUE."[".$result["date"].TE::BLUE."]"."\n".TE::GRAY."Damager".TE::WHITE.": ".TE::AQUA.$result["damager_name"]."\n".TE::GRAY."Cause".TE::WHITE.": ".TE::AQUA.$result["cause"]."\n".TE::GRAY."Item Name".TE::WHITE.": ".TE::AQUA.$result["item_name"]."\n".TE::GRAY."Coordinates".TE::WHITE.": ".TE::AQUA.$result["player_coordinates"]."\n".TE::GRAY."Elapsed".TE::WHITE.": ".TE::AQUA.Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
				}
			break;
			case "cwarn":
				if(!$this->testPermission($sender)){
					$sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
					return;
				}
				if(empty($args[1])){
					$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} {$args[0]} [string: playerName] [int: page]");
					return;
				}
				$page = 0;
				if(!empty($args[2])){
					$page = $args[2];
				}
				$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[1]);
				if($player instanceof Player){
					if(!Data::isWarned($player->getName())){
						$sender->sendMessage(TE::LIGHT_PURPLE.$player->getName()." was not warned before!");
						return;
					}
					$file = Data::getData("warns");
					$result = $file->get($player->getName());
					
					$results = array_chunk($result, 5);
					
					if(!isset($results[$page])) return;
					foreach($results[$page] as $index => $result){
						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." was warned, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_warn"].TE::RESET." ".TE::GRAY."Warned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}
				}else{
					if(!Data::isWarned($args[1])){
						$sender->sendMessage(TE::LIGHT_PURPLE.$args[1]." was not warned before!");
						return;
					}
					$file = Data::getData("warns");
					$result = $file->get($args[1]);
					
					$results = array_chunk($result, 5);
					
					if(!isset($results[$page])) return;
					foreach($results[$page] as $index => $result){
						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$args[1].TE::GRAY." was warned, for the reason".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["reason_of_warn"].TE::RESET." ".TE::GRAY."Warned by".TE::WHITE.": ".TE::LIGHT_PURPLE.$result["sender_name"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}
				}
			break;
			case "ccommands":
				if(!$this->testPermission($sender)){
					$sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
					return;
				}
				if(empty($args[1])){
					$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} {$args[0]} [string: playerName] [int: page]");
					return;
				}
				$page = 0;
				if(!empty($args[2])){
					$page = $args[2];
				}
				$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[1]);
				if($player instanceof Player){
					if(!Data::isCommandLog($player->getName())){
						$sender->sendMessage(TE::LIGHT_PURPLE.$player->getName()." does not run commands previously!");
						return;
					}
					$file = Data::getData("players_commands");
					$result = $file->get($player->getName());
					
					$results = array_chunk($result, 5);
					
					$pages = ceil(count($result) / 10);
	
	                if($page > $pages){
	                    return;
	                }
	                $sender->sendMessage(TE::BOLD.TE::GREEN."Commands List: ".TE::RESET.TE::AQUA.$page.TE::GRAY."/".TE::AQUA.$pages);
					if(!isset($results[$page])) return;
					foreach($results[$page] as $index => $result){
						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$player->getName().TE::GRAY." execute command".TE::WHITE.": ".TE::WHITE.$result["command_execute"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}
				}else{
					if(!Data::isCommandLog($args[1])){
						$sender->sendMessage(TE::LIGHT_PURPLE.$args[1]." does not run commands previously!");
						return;
					}
					$file = Data::getData("players_commands");
					$result = $file->get($args[1]);
					
					$results = array_chunk($result, 5);
					
					$pages = ceil(count($result) / 10);
	
	                if($page > $pages){
	                    return;
	                }
	                $sender->sendMessage(TE::BOLD.TE::GREEN."Commands List: ".TE::RESET.TE::AQUA.$page.TE::GRAY."/".TE::AQUA.$pages);
					if(!isset($results[$page])) return;
					foreach($results[$page] as $index => $result){
						$sender->sendMessage(AdvancedPractice::SYSTEM.TE::BLUE."[".$result["date"].TE::BLUE."]".TE::RESET." ".TE::LIGHT_PURPLE.$args[1].TE::GRAY." execute command".TE::WHITE.": ".TE::WHITE.$result["command_execute"].TE::RESET."\n".TE::GRAY."Elapsed".TE::WHITE.": ".Time::getTimeElapsed((int)microtime(true) - $result["elapsed"]));
					}
				}
			break;
			case "help":
			case "?":
				if(!$this->testPermission($sender)){
					$sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
					return;
				}
				$sender->sendMessage(TE::GREEN."Use: /{$label} cban [string: playerName] ".TE::GRAY."(Can review the information of a banned player)");
				$sender->sendMessage(TE::GREEN."Use: /{$label} cdeath [string: playerName] ".TE::GRAY."(Can check the last death of a player)");
				$sender->sendMessage(TE::GREEN."Use: /{$label} cwarn [string: playerName] [int: page] ".TE::GRAY."(Can see recent player warnings)");
				$sender->sendMessage(TE::GREEN."Use: /{$label} ccommands [string: playerName] [int: page] ".TE::GRAY."(Can check the player commands)");
			break;
		}
	}
}

?>