<?php

declare(strict_types = 1);

namespace Respawn;

use Respawn\Main;
use Respawn\Respawn;
use pocketmine\event\Listener as Events;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class Listener implements Events {

    public function __construct() {
        Main::getInstance()->getServer()->getPluginManager()->registerEvents($this, Main::getInstance());
    }

    public function onJoin(PlayerJoinEvent $event) {
        $event->setJoinMessage("");
    }

    public function onQuit(PlayerQuitEvent $event) {
        $event->setQuitMessage("");
    }
}