<?php


namespace Jacob\Orix\command;


use Jacob\Orix\AdvancedPractice;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class CapeCommand extends Command {
    const PREFIX = TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY;

    public function __construct() {
        parent::__construct("cape");
        $this->description = "Cape cosmetics.";
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
         /*   if (!$sender->hasPermission("capes.command.use")) {
                $sender->sendMessage(TextFormat::LIGHT_PURPLE . "You don't have permission to use this command.");
                return;
            }*/
            if (!isset($args[0])) {
                $sender->sendForm(AdvancedPractice::getInstance()->mainCapesForm());
                return;
            }
            if ($args[0] == "remove") {
                if (!isset($args[1])) {
                    $sender->sendMessage(TextFormat::LIGHT_PURPLE . "Please specify what cape you want to delete");
                    return;
                }
                $file = AdvancedPractice::getInstance()->getDataFolder() . "capes/" . $args[1] . ".png";
                if (is_file($file)) {
                   /* if (!$sender->hasPermission("capes.remove.use")) {
                        $sender->sendMessage(TextFormat::LIGHT_PURPLE . "You don't have the permission to use this command.");
                        return;
                    }*/
                    unlink($file);
                    $sender->sendMessage(TextFormat::GREEN . "Successfully removed the cape with the name " . TextFormat::GRAY . $args[1]);
                } else {
                    $sender->sendMessage(TextFormat::LIGHT_PURPLE . "Cape does not exist. Please make sure you typed in the right cape name.");
                }
            }
        } else {
            $sender->sendMessage("You can only use this command in-game.");
        }
    }
    }