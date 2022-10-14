<?php

declare(strict_types=1);

namespace juqn\pots;

use juqn\pots\entity\SplashPotion;
use juqn\pots\item\SplashPotionItem;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\PotionType;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\world\sound\ThrowSound;

class Pots extends PluginBase implements Listener
{
    
    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        EntityFactory::getInstance()->register(SplashPotion::class, function(World $world, CompoundTag $nbt): SplashPotion {
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort('PotionId', PotionTypeIds::WATER));
            
            if ($potionType === null) {
                throw new SavedDataLoadingException('No such potion type');
            }
            return new SplashPotion(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotion', 'minecraft:potion', 'thrownpotion']);
        
        foreach(PotionType::getAll() as $type)
            ItemFactory::getInstance()->register(new SplashPotionItem($type), true);
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $directionVector = $event->getDirectionVector();
        $item = $event->getItem();
        $player = $event->getPlayer();
        
        if ($item instanceof SplashPotionItem) {
            $event->cancel();
            
            $potion = new SplashPotion(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->yaw, $player->getLocation()->pitch), $player, $item->getPotionType());
            $potion->setMotion($directionVector->multiply(0.5));
            $projectileEv = new ProjectileLaunchEvent($potion);
            $projectileEv->call();
		
            if ($projectileEv->isCancelled()) {
                $potion->flagForDespawn();
                return;
            }
            $potion->spawnToAll();
            
            if (!$player->isCreative()) $player->getInventory()->setItemInHand(ItemFactory::air());
            $player->getLocation()->getWorld()->addSound($player->getLocation(), new ThrowSound());
        }
    }
}