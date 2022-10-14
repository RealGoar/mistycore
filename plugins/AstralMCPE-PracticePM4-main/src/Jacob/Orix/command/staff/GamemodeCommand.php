<?php

namespace Jacob\Orix\command\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\data\java\GameModeIdMap;
use pocketmine\player\GameMode;
use Jacob\Orix\AdvancedPractice;

use Jacob\Orix\task\asynctask\DiscordMessage;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;
use pocketmine\command\{Command, CommandSender};

class GamemodeCommand extends Command {
	
	/**
	 * GamemodeCommand Constructor.
	 */
	public function __construct(){
        $this->setPermission("gamemode.command.use");
		parent::__construct("gamemode");
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
			$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: gamemode]");
			return;
		}
		$gamemode = GameMode::fromString($args[0]);
		if($gamemode === null){
			$sender->sendMessage(TE::LIGHT_PURPLE."Unknown game mode");
			return;
		}
		if(isset($args[1])){
			if($args[1] === "@a"){
				foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
					$online->setGamemode($gamemode);
				}
				return;
			}
			$player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[1]);
			if(!$player instanceof Player){
				$sender->sendMessage(TE::LIGHT_PURPLE."The player you are looking for is not connected!");
				return;
			}
		}elseif($sender instanceof Player){
			$player = $sender;
		}
		$player->setGamemode($gamemode);
		if($player === $sender){
			foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
        		if($this->testPermission($online)){
					$online->sendMessage(TE::AQUA.$sender->getName().TE::WHITE." change his game mode for ".TE::AQUA.$gamemode->name());
				}
			}
			$message = "[".date("d/m/y H:i:s")."]"." ".$sender->getName()." change his game mode for ".$gamemode->name();
			AdvancedPractice::getInstance()->getServer()->getAsyncPool()->submitTask(new DiscordMessage(AdvancedPractice::getDefaultConfig("URL"), $message, "GamemodeLogger"));
		}else{
			foreach(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online){
        		if($this->testPermission($online)){
					$online->sendMessage(TE::AQUA.$player->getName().TE::WHITE." change his game mode for ".TE::AQUA.$gamemode->name().TE::WHITE." by ".TE::AQUA.$sender->getName());
				}
			}
			$message = "[".date("d/m/y H:i:s")."]"." ".$player->getName()." change his game mode for ".$gamemode->name()." by ".$sender->getName();
			AdvancedPractice::getInstance()->getServer()->getAsyncPool()->submitTask(new DiscordMessage(AdvancedPractice::getDefaultConfig("URL"), $message, "GamemodeLogger"));
		}
		if($gamemode !== $player->getGamemode()){
			$sender->sendMessage("Game mode change for " . $player->getName() . " failed!");
		}
        $webhook = new Webhook("https://discord.com/api/webhooks/1027904069936885792/CoXqMpDlcAxnbKSdcTl4WSkTBrqDU2HtjFNd9FdaKBB0zu8XP-eGZMAUJHhQZZzTr0oB");
        $embed = new Embed();
        $msg = new Message();
        $embed->setTitle("Gamemode Change");
        $senderName = $sender->getName();
        $gmname = $gamemode->name();
        $embed->setDescription("Player: $senderName\nCurrent Gamemode: $gmname");
        $msg->addEmbed($embed);
        $webhook->send($msg);
	}
}