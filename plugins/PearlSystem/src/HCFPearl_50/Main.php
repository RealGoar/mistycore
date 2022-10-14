<?php

declare(strict_types=1);

namespace HCFPearl_50;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\entity\EntityFactory;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\world\World;
use pocketmine\block\Opaque;
use pocketmine\block\Stair;
use pocketmine\block\FenceGate;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\Slab;
use pocketmine\block\Glass;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\math\Vector3;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\math\Facing;
class Main extends PluginBase implements Listener
{
	private static self $instance;
	
	public function onLoad(): void{
		self::$instance = $this;
	}
	
	public static function getInstance(): self{
		return self::$instance;
	}
	
	public function onEnable():void{
		EntityFactory::getInstance()->register(EPearl::class, function(World $world, CompoundTag $nbt) : EPearl{
			return new EpEarl(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityLegacyIds::ENDER_PEARL);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onLaunch(ProjectileLaunchEvent $event):void{
		$projectile = $event->getEntity();
		$player = $projectile->getOwningEntity();
		if($projectile instanceof EnderPearl){
			$projectile->kill();
			$block = $player->getTargetBlock(2); // Near Front Block
			$target = $player->getLineOfSight(1,1)[array_key_last($player->getLineOfSight(1,1))]; // Front of Block
			$other_target = $player->getLineOfSight(2,1)[array_key_last($player->getLineOfSight(2,1))]; // Front of target Block
			$motion = $player->getDirectionVector();
			$nbt = $this->createBaseNBT($player->getLocation(), $motion);
			$entity = new EPearl($player->getLocation(), $player, $nbt);
			if($entity instanceof EPearl){
				$event = new ProjectileLaunchEvent($entity);
				$event->call();
				if($event->isCancelled() && in_array($player->getGamemode()->getAliases(), ["spectator", "v", "view", "3"])){
					$entity->kill();
				}else{
					$entity->spawnToAll();
					$type = 0;
					$block_type = null;
					if($block){
						if($target){
							if($other_target){
								if($other_target->getId() !== 0){
									if($block instanceof FenceGate){
										return;
									}
									if($target->getId() !== 0 && $target instanceof Opaque && !$target instanceof EndPortalFrame){
										$entity->kill();
										$player->teleport($player->getPosition()->add(0.1, 0.5, 0.1));
										$player->getPosition()->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
										return;
									}
									if($target->getId() === 0 && !$block instanceof Slab){
										$type = 1;
									}
									if($block instanceof Slab || $block->getId() === 0) $block_type = $block;
									if((string)$target->getPosition()->asVector3() === (string)$player->getPosition()->getWorld()->getBlock($player->getPosition())->getPosition()->asVector3() && !$block_type){
										$entity->kill();
										$player->teleport($player->getPosition()->add(0.1, 0.5, 0.1));
										$player->getPosition()->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
										return;
									}
								}else{
									if($target->getId() === 0){
										return;
									}
								}
							}
						}
						if($block->getPosition()->getFloorX() === $player->getPosition()->getFloorX() && $block->getPosition()->getFloorZ() === $player->getPosition()->getFloorZ()) return;
						if($block instanceof Stair){
							if($player->getHorizontalFacing() === Facing::opposite($block->getFacing()) || $player->getHorizontalFacing() === $block->getFacing()){
								$entity->kill();
								$player->teleport($player->getPosition()->add(0.1, 0.5, 0.1));
								$player->getPosition()->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
								return;
							}
						}
						if($block instanceof Glass){
							return;
						}
						if(($distance = $block->getPosition()->distance($player->getPosition())) < 3){
							$entity->kill();
							$player->getPosition()->getWorld()->addParticle($origin = $player->getPosition(), new EndermanTeleportParticle());
							$player->getPosition()->getWorld()->addSound($origin, new EndermanTeleportSound());
							switch($entity->getHorizontalFacing()){
								case Facing::SOUTH:
									$target = $block->getPosition()->add(0, 0, 1);
								break;
								case Facing::WEST:
									$target = $block->getPosition()->add(-1, 0, 0);
								break;
								case Facing::NORTH:
									$target = $block->getPosition()->add(0, 0, -1);
								break;
								case Facing::EAST:
									$target = $block->getPosition()->add(1, 0, -0);
								break;
								default:
									$target = $block->getPosition()->add(0, 0, 0);
							}
							$old_position = $player->getPosition();
							$player->teleport($target); // Check teleport when its safe to tp 2 block must be air
							if($type === 1){
								$target = $target->add(0, 1, 0);
								if(!$player->getPosition()->getWorld()->getBlock($player->getPosition()->add(0, 1, 0))->canBeFlowedInto() || !$block->getPosition()->getWorld()->getBlock($player->getPosition()->add(0, 2, 0))->canBeFlowedInto()){
									$player->teleport(($target = $old_position)->add(0.1, 0.5, 0.1));
									$player->getPosition()->getWorld()->addSound($target, new EndermanTeleportSound());
									return;
								}
							}elseif($type === 0){
								if(!$player->getPosition()->getWorld()->getBlock($player->getPosition()->add(0, 1, 0))->canBeFlowedInto() || !$block->getPosition()->getWorld()->getBlock($player->getPosition())->canBeFlowedInto()){
									$player->teleport(($target = $old_position)->add(0.1, 0.5, 0.1));
									$player->getPosition()->getWorld()->addSound($target, new EndermanTeleportSound());
									return;
								}
							}
							
							$player->teleport($target->add(0.25, 0, 0.25)); // Final teleport
							$player->getPosition()->getWorld()->addSound($target, new EndermanTeleportSound());
						}
					}
				}
			}
		}
	}
	
	public function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag {
        return CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($pos->x),
                new DoubleTag($pos->y),
                new DoubleTag($pos->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0.0),
                new DoubleTag($motion !== null ? $motion->y : 0.0),
                new DoubleTag($motion !== null ? $motion->z : 0.0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }
}