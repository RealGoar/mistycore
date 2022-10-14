<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\{Command, CommandSender};

class KickCommand extends Command {
	
	/**
	 * KickCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("kick.command.use");
		parent::__construct("kick");
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
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName] [string: reason]");
			return;
		}
		$name = array_shift($args);
		$reason = "";
		for($i = 0; $i < count($args); $i++){
			$reason .= $args[$i];
			$reason .= " ";
		}
		$reason = substr($reason, 0, strlen($reason) - 1);
		$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($name);
		if(!$player instanceof Player){
			$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
   		 return;
		}
		if(empty($reason)){
			$reason = "Kicked by Admin";
		}
		$player->close("", TE::BOLD.TE::DARK_RED."You were kicked from our network".TE::RESET."\n".TE::LIGHT_PURPLE."Kicked By: ".TE::AQUA.$sender->getName()."\n".TE::LIGHT_PURPLE."Reason: ".TE::AQUA.$reason);
		AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$player->getName().TE::RESET.TE::GRAY." was kicked from the server by ".TE::BOLD.TE::AQUA.$sender->getName().TE::RESET.TE::GRAY." for the reason".TE::RESET.TE::WHITE.": ".TE::WHITE.$reason);
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $playerName = $player->getName();
        $sendern = $sender->getName();
        $embed->setDescription("Player Kicked: $playerName\nKicked By: $sendern\nReason: $reason");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
}

?>