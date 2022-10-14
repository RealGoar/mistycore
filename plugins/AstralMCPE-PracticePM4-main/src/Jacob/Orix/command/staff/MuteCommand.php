<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class MuteCommand extends Command {
    
    /**
     * MuteCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("mute.command.use");
        parent::__construct("mute");
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
        if(Data::isPermanentlyMuted($name)){
			$sender->sendMessage(TE::LIGHT_PURPLE.$name." has muted been silenced from the network!");
			return;
		}
		if(empty($reason)){
			$reason = "Muted by Admin";
		}
        Data::addMute($name, $sender->getName(), $reason, true);
        AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$name.TE::RESET.TE::GRAY." was permanently muted of the network by ".TE::BOLD.TE::AQUA.$sender->getName().TE::RESET.TE::GRAY." for the reason".TE::RESET.TE::WHITE.": ".TE::WHITE.$reason);
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $playerName = $name;
        $sendern = $sender->getName();
        $embed->setDescription("Player Muted: $playerName\nMuted By: $sendern\nReason: $reason");
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }
}

?>