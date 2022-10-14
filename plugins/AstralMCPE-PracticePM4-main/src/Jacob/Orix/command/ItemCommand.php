<?php namespace Jacob\Orix\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\TextFormat;
use Jacob\Orix\AdvancedPractice;

class ItemCommand extends Command {

    public static $ids = ["1","2","3","4","5","6","7","8","9","10","11","12","13"];

    public const PARTNER_ITEM_NAMES = [
        "switcher" => C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Switcher Ball",
    ];

	public function __construct() {
		parent::__construct("item", "Administrator command.");
	}

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if ($sender->hasPermission("item.cmd") === false) {
            $sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You lack sufficient permissions to access this command.");
            return;
        }
        if (!$sender instanceof Player) return;
        if(!isset($args[0])){
            $sender->sendMessage("§l§7[§d!§7] - §r§7Usage: /item give <id> <amount> <player>");
            return;
        }
        if(!isset($args[3])){
            $sender->sendMessage("§l§7[§d!§7] - §r§7Usage: /item give <id> <amount> <player>");
            return;
        }
        if(isset($args[0])){
            switch ($args[0]){
                case "give":
                    if(count($args) < 3){
                        $sender->sendMessage("§l§7[§d!§7] - §r§7Usage: /item give <id> <amount> <player>");
                    }elseif (in_array($args[1], self::$ids) && is_numeric($args[2]) && $player = Server::getInstance()->getPlayerByPrefix($args[3])){
                        $player = Server::getInstance()->getPlayerByPrefix($args[3]);
                        $player->sendMessage("§l§7[§d!§7] - §r§7You have been given " . $args[2] . " partner items.");
                        $this->giveCeBook($player, $args[1], $args[2]);  //$args1 is type
                    }else{
                        $sender->sendMessage("§l§7[§d!§7] - §r§7Usage: /item give <id> <amount> <player>");
                        return;
                    }
                    break;
            }
        }
    }

    public function giveCeBook(Player $player, string $ce, int $amount): void{
        switch ($ce){
            case "1":
                $item = VanillaItems::PUFFERFISH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Combo Ability")->setCount($amount);
                $item->setLore([
                    "§r§7Right-Click to receive\n§r§7strength two for 8 seconds",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "2":
                $item = VanillaItems::COOKIE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call")->setCount($amount);
                $item->setLore([
                    "§r§7Right-Click if under four hearts you are given \n§r§7Resistance 3, Regeneration 5, and Strength 2 for 6 seconds.",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "3":
                $item = VanillaItems::RED_DYE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Beserk Ability")->setCount($amount);
                $item->setLore([
                    "§r§7Right-Click to receive\n§r§7Strength 2, Resistance 3, and Regeneration 3 for 5 seconds",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "4":
                $item = VanillaItems::SLIMEBALL()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Effect Disabler")->setCount($amount);
                $item->setLore([
                    "§r§7Hit another player with this slimeball to\n§7clear the effects of the other player.",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "5":
                $item = VanillaItems::BLAZE_POWDER()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Strength II")->setCount($amount);
                $item->setLore([
                    "§r§7Right-Click to receive\n§7Strength 2 for 4 seconds!",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "6":
                $item = VanillaItems::IRON_INGOT()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Resistance III")->setCount($amount);
                $item->setLore([
                    "§r§7Right-Click to receive\n§7Resistance 3 for 4 seconds!",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "7":
                $item = VanillaItems::ROTTEN_FLESH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Starving Flesh")->setCount($amount);
                $item->setLore([
                    "§r§7Hit another player with this rotten flesh to \n§7set the hunger of the player to 1!",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "8":
                $item = VanillaItems::STICK()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Inventory Clogger")->setCount($amount);
                $item->setLore([
                    "§r§7Hit another player with this stick to \n§7clog the inventory of the player\nwith pickaxes!",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "9":
                $item = VanillaItems::DRAGON_HEAD()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Dragon Mask")->setCount($amount);
                $item->setLore([
                    "§r§a§lEffects\n§a - §7Speed 3\n§a - §7Haste 3\n§a - §7Strength\n§a - §7Resistance\n§a - §7Fire Resistance\n§a - §7Health Boost 3\n§a - §7Night Vision",
                ]);
                $player->getInventory()->addItem($item);
                break;
            case "10":
                $item = ItemFactory::getInstance()->get(ItemIds::SNOWBALL);
                $item->setCustomName(self::PARTNER_ITEM_NAMES["switcher"]);
                $item->setLore(["§r§7Hit another player with this snowball to\n§7switch positions with the player!"]);
                $item->setCount($amount);
                $item->getNamedTag()->setString("switcher", "lol");
                $player->getInventory()->addItem($item);
                break;
            case "11":
                $item = ItemFactory::getInstance()->get(ItemIds::CLOCK);
                $item->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Life Saver");
                $item->setLore(["§r§7This item saves your life!"]);
                $item->setCount($amount);
                $player->getInventory()->addItem($item);
                break;
            case "12":
                $item = ItemFactory::getInstance()->get(ItemIds::BONE);
                $item->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Bone");
                $item->setLore(["§r§7Makes it so the other player cant break, place, or open blocks and items."]);
                $item->setCount($amount);
                $player->getInventory()->addItem($item);
                break;
            case "13":
                $item = ItemFactory::getInstance()->get(ItemIds::NETHER_STAR);
                $item->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Ninja Star");
                $item->setLore(["§r§7Be sneaky and teleport to the other player!"]);
                $item->setCount($amount);
                $player->getInventory()->addItem($item);
                break;
        }
    }

}