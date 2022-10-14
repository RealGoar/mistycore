<?php

declare(strict_types=1);

namespace Jacob\Orix\particle;

use pocketmine\player\Player;
use pocketmine\world\particle\Particle;

class TrailParticle
{
    public static function display(Player $player, Particle $particle): void
    {
        $player->getWorld()->addParticle($player->getPosition()->add(0, 0.2, 0), $particle);
    }
}
