<?php


namespace Jacob\Orix\command;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\util\Utilities;
use pocketmine\entity\Skin;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class DisguiseCommand extends Command{

    private $plugin;

    public function __construct(AdvancedPractice $plugin){
        parent::__construct("disguise");
        $this->plugin=$plugin;
        $this->setDescription("Disguise your name.");
    }
    public function execute(CommandSender $player, string $commandLabel, array $args){
        if(!$player instanceof Player){
            return;
        }
        if ($player->hasPermission("disguise.cmd") === false) {
            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You lack sufficient permissions to access this command.");
            return;
        }
        if(!isset($args[0])){
            if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isDisguised()){
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setDisguised(false);
            }
            $names=Utilities::getFakeNames();
            $randname=$names[array_rand($names)];
            foreach($this->plugin->getServer()->getOnlinePlayers() as $online){
                if(Utilities::getPlayerDisplayName($online)==$randname or Utilities::getPlayerName($online)==$randname){
                    $player->sendMessage("§l§7[§d!§7] - §r§7 Disguise failed, name already in use.");
                    return;
                }
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setDisguised(true);
            $player->sendMessage("§l§7[§d!§7] - §r§7 You have been disguised as ".$randname."!");
            $player->setDisplayName($randname);
        }
        if(isset($args[0])){
            switch($args[0]){
                case "off":
                    if(!AdvancedPractice::getSessionManager()->getPlayerSession($player)->isDisguised()){
                        $player->sendMessage("§l§7[§d!§7] - §r§7 You are not in disguise.");
                        return;
                    }
                    $before=$player->getDisplayName();
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->setDisguised(false);
                    $player->setDisplayName($player->getName());
                    $player->sendMessage("§l§7[§d!§7] - §r§7 Disguise disabled.");
                    break;
                default:
                    $player->sendMessage("§l§7[§d!§7] - §r§7 You must provide a valid argument: off");
            }
        }
    }
}