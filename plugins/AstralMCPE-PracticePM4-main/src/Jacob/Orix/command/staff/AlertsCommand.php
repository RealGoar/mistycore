<?php

namespace Jacob\Orix\command\staff;

use pocketmine\player\Player;
use Jacob\Orix\anticheat\Alerts;

use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

class AlertsCommand extends Command {

    /**
     * AlertsCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("alerts.command.use");
        parent::__construct("alerts", "Enable or disable anticheat alerts");
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
        if(!$sender instanceof Player) {
            return;
        }
        if(Alerts::isEnable($sender)){
            Alerts::setDisable($sender);
            $sender->sendMessage(TE::LIGHT_PURPLE."You disabled the anticheat alerts for yourself!");
        }else{
            Alerts::setEnable($sender);
            $sender->sendMessage(TE::GREEN."You activated the anticheat alerts for you!");
        }
    }
}

?>