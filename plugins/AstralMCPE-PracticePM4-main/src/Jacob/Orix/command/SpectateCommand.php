<?php


namespace Jacob\Orix\command;


use Jacob\Orix\AdvancedPractice;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\item\ItemIds;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use Zinkil\pc\commands\GamemodeCommand;

class SpectateCommand extends Command
{
    private $plugin;

    public function __construct(AdvancedPractice $plugin){
        parent::__construct("spectate");
        $this->plugin=$plugin;
        $this->setDescription("Spectate other players that are in duels.");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::LIGHT_PURPLE . "Run this command in game.");
            return;
        }
        if ($sender->hasPermission("spectate.cmd") === false) {
            $sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You lack sufficient permissions to access this command.");
            return;
        }
        if (count($args) > 0) {
            //TODO: Implement off method
            //if($args[0] === "off"){
              //  $sender->setGamemode(GameMode::SURVIVAL());
            //}
            $player = $sender->getServer()->getPlayerExact($args[0]);
            if ($sender === $player) {
                $sender->sendMessage("§l§7[§d!§7] - §r§7You can't spectate yourself.");
                return;
            }

            //If the player is not online, it'll return null, and null equates to false, and the opposite of false is true
            if (!$player or !$player instanceof Player) {
                $sender->sendMessage("§l§7[§d!§7] - §r§7That player is not online.");
                return;
            }

            if (!AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()) {
                $sender->sendMessage("§l§7[§d!§7] - §r§7That player is not in a match.");
                return;
            }
            if(AdvancedPractice::getSessionManager()->getPlayerSession($sender)->isSpectator() === true){
                AdvancedPractice::getSessionManager()->getPlayerSession($sender)->setSpectator(false);
                $sender->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                $sender->getEffects()->clear();
                AdvancedPractice::getSessionManager()->getPlayerSession($sender)->giveHubKit();
                return;
            }
            $sender->getArmorInventory()->clearAll();
            $sender->getInventory()->clearAll();
            $sender->setGamemode(GameMode::CREATIVE());
            $sender->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 10000, 255, false));
            AdvancedPractice::getSessionManager()->getPlayerSession($sender)->setSpectator(true);
            $sender->teleport(new Location($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getWorld(), 0, 0));
            $sender->getInventory()->setItem(4, (VanillaItems::INK_SAC())->setCustomName(TextFormat::GREEN . "Spectator Toggle Off"));
            $sender->sendMessage("§l§7[§d!§7] - §r§7You have entered spectator mode. Type /spectator again to exit out of spectator mode.");
            return;
        }
    }
}