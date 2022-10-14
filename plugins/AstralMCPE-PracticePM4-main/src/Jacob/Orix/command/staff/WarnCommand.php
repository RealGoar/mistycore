<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\Data;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class WarnCommand extends Command {
    
    /**
     * WarnCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("warn.command.use");
        parent::__construct("warn", "Warn other players for bad behavior");
    }

    /**
     * @param CommandSender $sender
     * @param String $commandLabel
     * @param array $args
     * @return void
     */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
        if(!$this->testPermission($sender)){
            $sender->sendMessage(TE::LIGHT_PURPLE."You have not permissions to use this command");
            return;
        }
        if(empty($args)){
            $sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: target] [string: reason]");
            return;
        }
        $name = array_shift($args);
        $reason = "";
		for($i = 0; $i < count($args); $i++){
			$reason .= $args[$i];
			$reason .= " ";
		}
		$reason = substr($reason, 0, strlen($reason) - 1);
        Data::addWarn($name, $sender->getName(), $reason);
        AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM.TE::BOLD.TE::GREEN.$name.TE::RESET.TE::GRAY." was warned by ".TE::BOLD.TE::AQUA.$sender->getName().TE::RESET.TE::GRAY." for the reason".TE::RESET.TE::WHITE.": ".TE::WHITE.$reason);
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Punishment Logs");
        $accuser = $sender->getName();
        $embed->setDescription("Warned Player: $name\nWarned By: $accuser\nReason: $reason");
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }
}

?>