<?php
namespace Jacob\Orix\item;

use Jacob\Orix\AdvancedPractice;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemFactory;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\effect\EffectInstance;
use Jacob\Orix\task\NinjaStarTask;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;
use pocketmine\player\Player;

class ItemListener implements Listener
{
 public function onItemUse(PlayerItemUseEvent $event): void
 {
     $player = $event->getPlayer();
     $item = $event->getItem();
     switch ($item->getCustomName()) {
         case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Ninja Star":
             if ($player->getLastDamageCause() === null) {
                 $player->sendMessage("§l§7[§d!§7] - §r§7You'v tried using the Ninja Star but we couldn't find the player.");
                 return;
             }
             if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                 $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                 $event->cancel();
                 return;
             }
             if ($player->getLastDamageCause() instanceof Player)
             AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
             $item = $event->getItem();
             $item->pop();
             $player->getInventory()->setItemInHand($item);
             $cause = $player->getLastDamageCause();
             if ($cause === null) {
                 $player->sendMessage("§l§7[§d!§7] - §r§7You tried using the Ninja Star but we couldn't find the player.");
             }
             if (!$cause instanceof EntityDamageByEntityEvent) {
                 $player->sendMessage("§l§7[§d!§7] - §r§7You tried using the Ninja Star but we couldn't find the player.");
                 return;
             }
             $damager = $cause->getDamager();
             if (!$damager instanceof Player) {
                 $player->sendMessage("§l§7[§d!§7] - §r§7You tried using the Ninja Star but we couldn't find the player.");
                 return;
             }
             if ($cause === null) {
                 $player->sendMessage("§l§7[§d!§7] - §r§7You tried using the Ninja Star but we couldn't find the player.");
             }
             $damager->sendMessage("§l§4WARNING - Player teleporting!");
                $player->sendMessage("§l§7[§d!§7] - §r§7" . "You are teleporting to the other player in 3 seconds.");
                $player->sendMessage("§l§7[§d!§7] - §r§7" . "You are now on a cooldown for 10 seconds");
                AdvancedPractice::getInstance()->getScheduler()->scheduleRepeatingTask(new NinjaStarTask($player, $damager), 20);
                break;

            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Life Saver":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->sendMessage("§l§7[§d!§7] - §r§7Life Saver Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 6, 4));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 6, 4));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 20 * 60, 1));
                break;
            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Combo Ability":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                $player->sendMessage("§l§7[§d!§7] - §r§7Combo Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 8, 1));
                break;
            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                if($player->getHealth() > 5){
                    $player->sendMessage("§l§7[§d!§7] - §r§7You do not have 4 hearts or less!");
                    return;
                }
                if($player->getHealth() <= 8)
                    $player->sendMessage("§l§7[§d!§7] - §r§7Close Call Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 6, 2));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 6, 4));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 6, 1));
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                    return;
            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Beserk Ability":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                    $player->sendMessage("§l§7[§d!§7] - §r§7Beserk Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 5, 2));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 5, 2));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 1));
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                return;
            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Strength II":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                $player->sendMessage("§l§7[§d!§7] - §r§7Strength II Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 4, 1));
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                return;
            case C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Resistance III":
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown() > 0) {
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on an item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartnerItemCooldown()) . ")");
                    $event->cancel();
                    return;
                }
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPartnerItemCooldown();
                $player->sendMessage("§l§7[§d!§7] - §r§7Resistance III Ability Activated.");
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 4, 2));
                $item = $event->getItem();
                $item->pop();
                $player->getInventory()->setItemInHand($item);
                return;
                }
        }
    public function onDamage2(EntityDamageByEntityEvent $event){
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if($entity instanceof Player && $damager instanceof Player && $damager->getInventory()->getItemInHand()->getCustomName() === C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Effect Disabler"){
            if (AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown() > 0) {
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($damager)->setPartnerItemCooldown();
            $entity->getEffects()->clear();

            $entity->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
            $name = $entity->getName();
            $entity->sendMessage("§l§7[§d!§7] - §r§7Your effects were cleared since someone used the 'Effect Disabler' item against you.");
            $damager->sendMessage("§l§7[§d!§7] - §r§7You have cleared the effects of $name.");
            $item = $damager->getInventory()->getItemInHand();
            $item->pop();
            $damager->getInventory()->setItemInHand($item);
        }
        if($entity instanceof Player && $damager instanceof Player && $damager->getInventory()->getItemInHand()->getCustomName() === C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Starving Flesh"){
            if (AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown() > 0) {
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($damager)->setPartnerItemCooldown();
            $entity->getHungerManager()->setFood(2);
            $name = $entity->getName();
            $entity->sendMessage("§l§7[§d!§7] - §r§7Your hunger was removed since someone used the 'Starving Flesh' item against you.");
            $damager->sendMessage("§l§7[§d!§7] - §r§7You have cleared the hunger of $name.");
            $item = $damager->getInventory()->getItemInHand();
            $item->pop();
            $damager->getInventory()->setItemInHand($item);
        }
        if($entity instanceof Player && $damager instanceof Player && $damager->getInventory()->getItemInHand()->getCustomName() === C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Inventory Clogger"){
            if (AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown() > 0) {
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($damager)->setPartnerItemCooldown();
            $entity->getInventory()->addItem(ItemFactory::getInstance()->get(270, 0, 36));
            $entity->sendMessage("§l§7[§d!§7] - §r§7Your inventory was clogged since someone used the 'Inventory Clogger' item against you.");
            $name = $entity->getName();
            $damager->sendMessage("§l§7[§d!§7] - §r§7You have clogged the inventory of $name.");
            $item = $damager->getInventory()->getItemInHand();
            $item->pop();
            $damager->getInventory()->setItemInHand($item);
        }
        if($entity instanceof Player && $damager instanceof Player && $damager->getInventory()->getItemInHand()->getCustomName() === C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Bone") {
            if (AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown() > 0) {
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getPartnerItemCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($damager)->setPartnerItemCooldown();
            $entity->sendMessage("§l§7[§d!§7] - §r§7You can no longer place, break, open anything for 10 seconds since someone used the 'Bone' item against you.");
            AdvancedPractice::getSessionManager()->getPlayerSession($entity)->setBone();
            $item = $damager->getInventory()->getItemInHand();
            $item->pop();
            $damager->getInventory()->setItemInHand($item);
        }
    }
    public function checkForMask(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        if($player->getArmorInventory()->getHelmet()->getCustomName() === "§r§5§lDragon Mask") {
            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 2000, 2));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::HASTE(), 20 * 2000, 2));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2000, 0));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 2000, 0));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 2000, 0));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::HEALTH_BOOST(), 20 * 2000, 1));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 20 * 2000, 1));
        }
        if($player->getArmorInventory()->getHelmet()->isNull()){
            $player->getEffects()->clear();
            $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
        }
    }
    public function onHitByProjectile(ProjectileHitEntityEvent $event) : void {
        $hit = $event->getEntityHit();
        if ($hit instanceof Player) {
            $entity = $event->getEntity();
            $owner = $entity->getOwningEntity();
            if ($owner instanceof Player) {
                if ($entity instanceof \pocketmine\entity\projectile\Snowball) {
                    if (AdvancedPractice::getSessionManager()->getPlayerSession($owner)->getPartnerItemCooldown() > 0) {
                        $owner->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a item cooldown! §7(" . AdvancedPractice::getUtils()->secondsToPartnerItemCD(AdvancedPractice::getSessionManager()->getPlayerSession($owner)->getPartnerItemCooldown()) . ")");
                        return;
                    }
                    AdvancedPractice::getSessionManager()->getPlayerSession($owner)->setPartnerItemCooldown();
                    $owner->sendMessage("§l§7[§d!§7] - §r§7You have switchered §7" . $hit->getName() . "§7!");
                    $pos1 = $owner->getPosition();
                    $pos2 = $hit->getPosition();
                    $hit->teleport($pos1);
                    $hit->sendMessage("§l§7[§d!§7] - §r§7" . $owner->getName() . " §7has switchered you!");
                    $owner->teleport($pos2);
                }
            }
        }
    }
}