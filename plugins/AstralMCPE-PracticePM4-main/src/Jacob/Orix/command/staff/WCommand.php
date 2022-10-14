<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use Jacob\Orix\AdvancedPractice;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class WCommand extends Command {
	
	/**
	 * WCommand Constructor.
	 */
	public function __construct(){
		parent::__construct("w");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(empty($args)){
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName] [string: message]");
			return;
		}
		$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix(array_shift($args));
		if(!$player instanceof Player){
			$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
			return;
		}
		$message = implode(" ", $args);
		
		$sender->sendMessage(TE::GRAY."(".TE::LIGHT_PURPLE."To ".TE::GRAY.$player->getName().TE::GRAY.")".TE::RESET." ".TE::WHITE.$message);
		$player->sendMessage(TE::GRAY."(".TE::LIGHT_PURPLE."From ".TE::GRAY.$sender->getName().TE::GRAY.")".TE::RESET." ".TE::WHITE.$message);
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Private Chat Logs");
        $senderName = $sender->getName();
        $playerName = $player->getName();
        $embed->setDescription("Delivering Player: $senderName\nReceiving Player: $playerName\nMessage: $message");
        $msg->addEmbed($embed);
	}
}

?>
