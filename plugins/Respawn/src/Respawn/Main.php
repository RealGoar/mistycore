<?php

declare(strict_types = 1);

namespace Respawn;

use Respawn\Utils\Storage;
use Respawn\Command;
use Respawn\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        new Storage();
        new Command();
        new Listener();
    }
}