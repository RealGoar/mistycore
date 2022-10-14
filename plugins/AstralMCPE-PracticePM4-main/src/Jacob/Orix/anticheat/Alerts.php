<?php

namespace Jacob\Orix\anticheat;

use Jacob\Orix\Data\PlayerBase;
use Jacob\Orix\AdvancedPractice;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;

class Alerts
{

    /** @var array[] */
    protected static array $alerts = [];

    /**
     * @param Player $player
     * @return bool
     */
    public static function isEnable(Player $player): bool
    {
        if (isset(self::$alerts[$player->getName()])) {
            return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function setEnable(Player $player): void
    {
        self::$alerts[$player->getName()] = $player;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function setDisable(Player $player): void
    {
        unset(self::$alerts[$player->getName()]);
    }

    /**
     * @param Player $damager
     * @param String $type
     * @return void
     */
    public static function send(Player $damager, string $type): void
    {
        foreach (PlayerBase::getStaffs() as $player) {
            if (!self::isEnable($player)) return;

            if ($type === "reach") $player->sendMessage(AdvancedPractice::ANTICHEAT . TE::DARK_RED . $damager->getName() . TE::GRAY . " is using Reach or some kind of perks " . TE::BLUE . "[" . TE::YELLOW . "0" . TE::BLUE . "]");
        }
    }
}