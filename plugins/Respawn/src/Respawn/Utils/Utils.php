<?php

declare(strict_types = 1);

namespace Respawn\Utils;

use Respawn\Main;
use pocketmine\world\Position;

class Utils {

    public static function getDataFolder(): String {
        return Main::getInstance()->getDataFolder();
    }

    public static function getWorld(string $world) {
        return Main::getInstance()->getServer()->getWorldManager()
        ->getWorldByName($world);
    }

    public static function positionToArray(Position $pos): array {
        $newPos = $pos->add(0, 1, 0);
        return [
            "world" => $pos->getWorld()->getFolderName(),
            "x" => $newPos->getX(),
            "y" => $newPos->getY(),
            "z" => $newPos->getZ()
        ];
    }

    public static function arrayToPosition(array $pos): Position {
        return new Position($pos['x'], $pos['y'], $pos['z'], self::getWorld($pos['world']));
    }
}