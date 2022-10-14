<?php

namespace fayu;

use fayu\cmd\AlertCommand;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {


    protected static Main $instance;

    public function onLoad(): void
    {
        self::$instance = $this;
    }


    public function OnEnable() : void {

        $this->getServer()->getCommandMap()->register("/alert", new AlertCommand());
        $this->getLogger()->info("TebexLogs Shop plugin by xN3wVerz#2496");
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }
}