<?php
namespace Jacob\Orix\translation;

use Jacob\Orix\exception\TranslationFailedException;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

class Translation {

    public static function translate(string $translation) : string {
        switch ($translation) {
            case "noPermission":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Not showing command information due to self-leak issues.";
            case "playerNotFound":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is not online.";
            case "playerAlreadyBanned":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is already banned.";
            case "ipAlreadyBanned":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is already IP banned.";
            case "ipNotBanned":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 IP address is not banned.";
            case "ipAlreadyMuted":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 IP address is already muted.";
            case "playerNotBanned":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is not banned.";
            case "playerAlreadyMuted":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is already muted.";
            case "playerNotMuted":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is not muted.";
            case "ipNotMuted":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 IP address is not muted.";
            case "playerAlreadyBlocked":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is already blocked.";
            case "playerNotBlocked":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 Player is not blocked.";
            case "ipAlreadyBlocked":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 IP address is already blocked.";
            case "ipNotBlocked":
                return TextFormat::RESET . "§r§l§7[§d!§7] - §r§7 IP address is not blocked.";
            default:
                throw new TranslationFailedException("Failed to translate.");
        }
    }

    public static function translateParams(string $translation, array $parameters) : string {
        if (empty($parameters)) {
            throw new InvalidArgumentException("Parameter is empty.");
        }
        switch ($translation) {
            case "usage":
                $command = $parameters[0];
                if ($command instanceof Command) {
                    return TextFormat::RESET . "§r§l§8(§5USAGE§8)§r§7 " . $command->getUsage();
                } else {
                    throw new InvalidArgumentException("Parameter index 0 must be the type of Command.");
                }
        }
        return $translation;
    }
}
