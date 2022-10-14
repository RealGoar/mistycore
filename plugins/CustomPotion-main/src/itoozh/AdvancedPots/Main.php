<?php

namespace itoozh\AdvancedPots;

use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\PotionType;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitBlockEvent;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onHit(ProjectileHitBlockEvent $event): void{
        $projectile = $event->getEntity();

        if($projectile instanceof SplashPotion && $projectile->getPotionType()->equals(PotionType::STRONG_HEALING())){
            $player = $projectile->getOwningEntity();

            if($player instanceof Player){
                $distance = $projectile->getPosition()->distance($player->getPosition());

                if($distance <= 3.5 && $player->isAlive()){
                    $health = $player->getHealth() + 4.3;
                    $player->setHealth($health > $player->getMaxHealth() ? $player->getMaxHealth() : $health);
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void{
        $item = $event->getItem();

        if($item instanceof \pocketmine\item\SplashPotion){
            $player = $event->getPlayer();
            $item->onClickAir($player, $player->getDirectionVector());
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
        }
    }

}