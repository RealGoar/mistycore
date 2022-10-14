<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\PlayerBase;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\{Command, CommandSender};

class ReportCommand extends Command {
	
	/**
	 * ReportCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("report.command.use");
		parent::__construct("report", "Report someone to do for the staffs");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(empty($args)){
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName] [string: reason]");
			return;
		}
		$name = array_shift($args);
		$motive = implode(" ", $args);
		
		$date = date("d/m/Y H:i:s");
		
		$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($name);
		if(!$player instanceof Player){
			$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
   		 return;
		}
        if($sender instanceof Player) {
            $this->sendReport($player, $sender, $motive);
        }
	}
		
	/**
	 * @param Player $player
	 * @param Player $sender
	 * @param String $motive
	 */
	protected function sendReport(Player $player, Player $sender, String $motive){
		$sender->sendMessage(TE::GRAY."You reported to the player ".TE::DARK_RED.$player->getName());
		foreach(PlayerBase::getStaffs() as $pl){
			$pl->sendMessage(
			TE::GRAY."=========================="."\n".
			TE::YELLOW."Accused: ".TE::WHITE.$player->getName()."\n".
			TE::YELLOW."Accuser: ".TE::WHITE.$sender->getName()."\n".
			TE::YELLOW."Reason: ".TE::WHITE.$motive."\n".
			TE::YELLOW."Connection: ".TE::WHITE.$player->getNetworkSession()->getPing()."\n".
			TE::YELLOW."World: ".TE::WHITE.$player->getWorld()->getFolderName()."\n".
			TE::GRAY."=========================="
			);
		}
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Report");
        $accused = $player->getName();
        $accuser = $sender->getName();
        $embed->setDescription("Accused Player: $accused\nAccuser: $accuser\nReason: $motive");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
}