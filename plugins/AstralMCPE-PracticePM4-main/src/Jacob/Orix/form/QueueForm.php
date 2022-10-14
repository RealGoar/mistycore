<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use cosmicpe\form\types\Icon;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\duel\Queue;

class QueueForm extends SimpleForm {

	public function __construct(bool $isRanked = false)
    {
        parent::__construct(($isRanked ? "Ranked" : "Un-Ranked") . " Queue", "Tap a mode to queue for.");
        foreach (AdvancedPractice::getInstance()->modes as $name => $modeInfo) {
            $queued = false;
            foreach (AdvancedPractice::getInstance()->queues as $class) {
                if ($class instanceof Queue) {
                    if ($class->isRanked() == $isRanked and $class->getMode() == $name) {
                        $queued = true;
                    }
                }
            }
            //Because if someones queued, its never gonna go over two. This reason being that once theirs two it makes duel lol.

            if ($name === "Sumo") {
                continue;
            } else {
                if($name === "BuildUHC"){
                    return;
                }
                 if($name === "Knockback"){
                    return;
                }
                $this->addButton(new Button(($isRanked ? "Ranked " : "Un-Ranked ") . $name . "\nQueued: " . ($queued ? "1" : "0"), new Icon(Icon::PATH, $modeInfo["icon"])), function (Player $player, int $index) use ($isRanked, $name) {
                    if($name === "BaseRaiding") {
                        if($player->hasPermission("choose.pref")){
                            $player->getArmorInventory()->clearAll();
                            $player->sendMessage("§l§7[§d!§7] - §r§7Choose what role you would like to have for this duel by interacting with the items in your inventory.");
                            $player->getInventory()->clearAll();
                            $trapper = VanillaItems::DIAMOND()->setCustomName("§7[§b§lTRAPPER§r§7] (Right-Click)");
                            $player->getInventory()->setItem(3, $trapper);
                            $raider = VanillaItems::BRICK()->setCustomName("§7[§d§lRAIDER§r§7] (Right-Click)");
                            $player->getInventory()->setItem(5, $raider);
                            return;
                        }
                    }
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->createQueue($name, $isRanked);
                });
            }
        }
    }

}