<?php

declare(strict_types=1);

namespace Jacob\Orix\particle;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\Particle;

class WingParticle
{
    /**
     * @var ThreeDimensionalPoint[]
     */
    protected static array $outline;
    /**
     * @var ThreeDimensionalPoint[]
     */
    protected static array $fill;

    public static function loadPositions(): void
    {
        self::$outline[] = new ThreeDimensionalPoint(0, 0, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.1, 0.01, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.3, 0.03, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.3, 0.03, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.4, 0.04, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.6, 0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.61, 0.2, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.61, 0.2, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.62, 0.4, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.63, 0.6, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.635, 0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.7, 0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.9, 0.75, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, 0.8, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.9, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.6, 1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.8, 1.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.85, 0.9, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.9, 0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.85, 0.5, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.8, 0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.75, 0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.7, -0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.65, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.55, -0.5, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.45, -0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.30, -0.75, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.15, -0.8, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.0, -0.85, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.8, -0.87, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.6, -0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.5, -0.5, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.4, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.3, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.15, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.9, 0.55, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, 0.6, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.6, 0.9, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.9, 0.35, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, 0.4, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.5, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.6, 0.7, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.9, 0.15, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, 0.2, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.6, 0.5, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.9, -0.05, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, 0, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.6, 0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.7, -0.25, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.0, -0.2, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, -0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, 0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(0.7, -0.45, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.0, -0.4, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.2, -0.3, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.4, -0.1, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.30, -0.55, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.15, -0.6, -0.5);
        self::$outline[] = new ThreeDimensionalPoint(1.0, -0.65, -0.5);

        self::$fill[] = new ThreeDimensionalPoint(1.2, 0.6, -0.5);
        self::$fill[] = new ThreeDimensionalPoint(1.4, 0.7, -0.5);
        self::$fill[] = new ThreeDimensionalPoint(1.1, 0.2, -0.5);
        self::$fill[] = new ThreeDimensionalPoint(1.3, 0.3, -0.5);
        self::$fill[] = new ThreeDimensionalPoint(1.0, -0.2, -0.5);
        self::$fill[] = new ThreeDimensionalPoint(1.2, -0.1, -0.5);
    }

    public static function display(Player $player, Particle $particle, ?Particle $fill = null): void
    {
        $playerLocation = $player->getLocation();
        $x = (float)$player->getEyePos()->getX();
        $y = (float)$player->getEyePos()->getY() - 0.2;
        $z = (float)$player->getEyePos()->getZ();
        $rot = -$playerLocation->getYaw() * 0.017453292;

        foreach (self::$outline as $point) {
            $rotated = $point->rotate($rot);

            $player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $particle);

            $point->z *= -1;
            $rotated = $point->rotate($rot + 3.1415);
            $point->z *= -1;

            $player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $particle);
        }

        if ($fill) {
            foreach (self::$fill as $point) {
                $rotated = $point->rotate($rot);

                $player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $fill);

                $point->z *= -1;
                $rotated = $point->rotate($rot + 3.1415);
                $point->z *= -1;

                $player->getWorld()->addParticle(new Vector3($rotated->x + $x, $rotated->y + $y, $rotated->z + $z), $fill);
            }
        }
    }
}
