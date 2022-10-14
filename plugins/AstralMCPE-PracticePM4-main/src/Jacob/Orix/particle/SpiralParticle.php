<?php

declare(strict_types=1);

namespace Jacob\Orix\particle;

use pocketmine\player\Player;
use pocketmine\world\particle\Particle;

class SpiralParticle
{
    public static function display(Player $player, Particle $particle): void
    {
        $slice = 2 * M_PI / 16;
        $radius = 0.65;
        $playerOffset = 2;
        for ($i = 0; $i < 16; $i++) {
            $angle = $slice * $i;
            $dx = $radius * cos($angle);
            $dy = $playerOffset;
            $dz = $radius * sin($angle);
            $player->getWorld()->addParticle($player->getPosition()->add($dx, $dy, $dz), $particle);
        }
    }

//    protected int $stepX = 0;
//    protected int $particles = 12;
//    protected int $particlesPerRotation = 90;
//    protected float $radius = 0.8;
//
//    public function display(Player $player, Particle $particle): void
//    {
//        for ($stepY = -60; $stepY < 60; $stepY += 120 / $this->particles) {
//            $dx = -(cos((($this->stepX + $stepY) / (double)$this->particlesPerRotation) * M_PI * 2)) * $this->radius;
//            $dy = $stepY / $this->particlesPerRotation / 2;
//            $dz = -(sin((($this->stepX + $stepY) / (double)$this->particlesPerRotation) * M_PI * 2)) * $this->radius;
//            $player->getWorld()->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
//        }
//    }
//
//    public function updateTimers(): void
//    {
//        $this->stepX++;
//    }
}
