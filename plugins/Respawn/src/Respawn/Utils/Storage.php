<?php

declare(strict_types = 1);

namespace Respawn\Utils;

use Respawn\Utils\Utils;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

final class Storage {

    use SingletonTrait;

    private $config;
    private $positions = [];

    public function __construct() {
        self::setInstance($this);
        $this->config = new Config(Utils::getDataFolder() . 'positions.yml', Config::YAML);
        $this->load();
    }

    private function load() {
        $this->positions = $this->config->getAll();
    }

    public function register(array $position) {
        $this->config->set("spawn-" . count($this->positions), $position);
        $this->config->save();
        $this->positions["spawn-" . count($this->positions)] = $position;
    }

    public function getRandomPosition() {
        if (count($this->positions) === 0) {
            return null;
        }

        return Utils::arrayToPosition($this->positions[array_rand($this->positions)]);
    }
}