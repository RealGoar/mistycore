<?php

namespace Jacob\Orix;

use Jacob\Orix\util\date\Countdown;
use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PlayerPreLoginListener implements Listener {
    
    public function onPlayerPreLogin(PlayerPreLoginEvent $event) {
        $username = $event->getPlayerInfo()->getUsername();

        $banList = Server::getInstance()->getNameBans();
        if ($banList->isBanned(strtolower($username))) {
            $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($username)];

            if ($entry->getExpires() == null) {
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are still death banned. Reason:§5 " . $reason . ".";
                } else {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are currently banned by§1 " . ($entry->getSource());
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $banList->remove($entry->getName());
                    return;
                }
                $banReason = $entry->getReason();
                if ($banReason != null || $banReason != "") {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are still death banned.\n§7Reason:§5 "  . $banReason ."\n§7You will be able to play again in§5 ". $expiry .".";
                } else {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are still death banned. §bYou will be able to play again in §5" . $expiry . ".";
                }
            }
            $event->setKickReason(0, $kickMessage);
        }
    }
    
    public function onPlayerPreLogin2(PlayerPreLoginEvent $event) {
        $banList = Server::getInstance()->getIPBans();

        if ($banList->isBanned(strtolower($event->getIp()))) {
            $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($event->getIp())];

            if ($entry->getExpires() == null) {
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are currently IP banned by §5Staff\n§7Reason:§5 " . $reason .".";
                } else {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are currently IP banned by §5Staff";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $banList->remove($entry->getName());
                    return;
                }
                $banReason = $entry->getReason();
                if ($banReason != null || $banReason != "") {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are currently IP banned by §5Staff\n§7Reason:§5 " . $banReason . " §7until§5 " . $expiry .".";
                } else {
                    $kickMessage = TextFormat::LIGHT_PURPLE . "§r§l§7[§d!§7] - §r§7 You are currently IP banned by §5Staff\n§7Until:§5 ". $expiry .".";
                }
            }
            $event->setKickReason(0, $kickMessage);
        }
    }
}
