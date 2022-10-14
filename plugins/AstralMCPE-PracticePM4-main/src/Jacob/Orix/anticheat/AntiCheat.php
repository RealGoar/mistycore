<?php

namespace Jacob\Orix\anticheat;

use Jacob\Orix\AdvancedPractice;


use pocketmine\player\Player;
use pocketmine\entity\Entity;

class AntiCheat
{

    /**
     * @param Player $player
     * @param Entity $entity
     * @return bool
     */
    public static function haveReach(Player $player, Entity $entity): bool
    {
        $maxreach = AdvancedPractice::getDefaultConfig("max-player-reach");
        $minping = AdvancedPractice::getDefaultConfig("min-player-ping");
        if ((int)$player->getPosition()->distance($entity->getPosition()) > $maxreach && (int)$player->getNetworkSession()->getPing() < $minping) {
            return true;
        }
        return false;
    }
}