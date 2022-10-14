<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use Jacob\Orix\util\Time;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class MuteTimeCommand extends Command {
	
	/**
	 * MuteTimeCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("tmute.command.use");
		parent::__construct("tmute");
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
		if(Data::isTemporarilyMuted($name)){
			$sender->sendMessage(TE::LIGHT_PURPLE.$name." has muted been silenced from the network!");
			return;
		}
		if(empty($reason)){
			$reason = "Muted by Admin";
		}
        Data::addMute($name, $reason, $sender->getName(), false, Time::getFormatTime(Time::stringToInt($time), $time));
		AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$name.TE::RESET.TE::GRAY." was temporarily muted of the network by ".TE::BOLD.TE::AQUA.$sender->getName().TE::RESET.TE::GRAY." for the reason".TE::RESET.TE::WHITE.": ".TE::WHITE.$reason);
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $playerName = $name;
        $sendern = $sender->getName();
        $embed->setDescription("Player Muted: $playerName\nMuted By: $sendern\nReason: $reason\nTime: $time");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
}

?>