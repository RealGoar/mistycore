<?php

namespace Jacob\Orix\command\staff;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use Jacob\Orix\util\Time;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class BanTimeCommand extends Command {
	
	/**
	 * BanTimeCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("tban.command.use");
		parent::__construct("tban");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $commandLabel
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(!$this->testPermission($sender)){
			return;
		}
		if(empty($args)){
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName] [int: time] [string: reason]");
			return;
		}
		if(!in_array(Time::intToString($args[1]), Time::VALID_FORMATS)){
			$sender->sendMessage(TE::LIGHT_PURPLE."The time format you enter is invalid!");
			return;
		}
		$time = $args[1];
		$name = array_shift($args);
		$reason = "";
		for($i = 1; $i < count($args); $i++){
			$reason .= $args[$i];
			$reason .= " ";
		}
		$reason = substr($reason, 0, strlen($reason) - 1);
		if(Data::isTemporarilyBanned($name)){
			$sender->sendMessage(TE::LIGHT_PURPLE.$name." has already been banned from the network!");
			return;
		}
		$file = Data::getData("players");
        $address = "";
        $uuid = "";
        if($file->exists($name)){
        	$address = $file->get($name)["address"];
        	$uuid = $file->get($name)["uuid"];
        }
        if(empty($reason)){
			$reason = "Banned by Admin";
		}
        Data::addBan($name, $reason, $sender->getName(), false, Time::getFormatTime(Time::stringToInt($time), $time), $address, $uuid);
        if(($player = AdvancedPractice::getInstance()->getServer()->getPlayerExact($name)) instanceof Player){
        	$player->close("", TE::BOLD.TE::LIGHT_PURPLE."You were banned from the server temporarily".TE::RESET."\n".TE::GRAY."You were banned by: ".TE::AQUA.$sender->getName().TE::RESET."\n".TE::GRAY."Reason: ".TE::AQUA.$reason.TE::RESET."\n".TE::GRAY."Date: ".TE::AQUA.date("d/m/y H:i:s").TE::RESET."\n".TE::BLUE."Discord: ".TE::AQUA."https://discord.gg/4aEFcayfch");
        }
		AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$name.TE::RESET.TE::GRAY." was temporarily banned of the network by ".TE::BOLD.TE::AQUA.$sender->getName().TE::RESET.TE::GRAY." for the reason".TE::RESET.TE::WHITE.": ".TE::WHITE.$reason);
	}
}

?>