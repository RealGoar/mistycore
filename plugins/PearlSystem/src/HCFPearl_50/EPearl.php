<?php

declare(strict_types=1);

namespace HCFPearl_50;

use pocketmine\player\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\Random;
use pocketmine\item\VanillaItems;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\nbt\tag\CompoundTag;
class EPearl extends Projectile
{
	public static function getNetworkTypeId() : string{ return EntityIds::ENDER_PEARL; }
	protected $gravity = 0.1;
	
	protected function getInitialSizeInfo():EntitySizeInfo{
		return new EntitySizeInfo(0.2, 0.2);
	}
	
	public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
		parent::__construct($location, $shootingEntity, $nbt);
		if($shootingEntity instanceof Player){
			$this->setPosition($this->getPosition()->add(0, $shootingEntity->getEyeHeight(), 0));
			$this->setMotion($shootingEntity->getDirectionVector()->multiply(1));
            $this->handleMotion($this->motion->x, $this->motion->y, $this->motion->z, 1.7, 0);
		}
	}
	
	public function getResultDamage():int{
		return -1;
	}
	
	public function handleMotion(float $x, float $y, float $z, float $f1, float $f2){
		$rand = new Random();
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x = $x / (float)$f;
		$y = $y / (float)$f;
		$z = $z / (float)$f;
		$x = $x + $rand->nextSignedFloat() * 0.007499999832361937 * (float)$f2;
		$y = $y + $rand->nextSignedFloat() * 0.008599999832361937 * (float)$f2;
		$z = $z + $rand->nextSignedFloat() * 0.007499999832361937 * (float)$f2;
		$x = $x * (float)$f1;
		$y = $y * (float)$f1;
		$z = $z * (float)$f1;
		$this->motion->x += $x;
		$this->motion->y += $y * 1.40;
		$this->motion->z += $z;
	}
	
	protected function onHit(ProjectileHitEvent $event):void{
		$owner = $this->getOwningEntity();
		if($owner === null) return;
		$this->getWorld()->addParticle($origin = $owner->getPosition(), new EndermanTeleportParticle());
		$this->getWorld()->addSound($origin, new EndermanTeleportSound());
		$owner->teleport($target = $event->getRayTraceResult()->getHitVector());
		$target_1 = $target->add(0, 1, 0);
		if(!$owner->getPosition()->getWorld()->getBlock($owner->getPosition()->add(0, 1, 0))->canBeFlowedInto()){
            if($owner instanceof HCFPlayer){
                $owner->getInventory()->addItem(VanillaItems::ENDER_PEARL());
                $owner->sendTip("§cPearl Refunded");
            }

			$owner->teleport(($target_1 = $origin)->add(0.1, 0.5, 0.1));
			$owner->getPosition()->getWorld()->addSound($target_1, new EndermanTeleportSound());
			$this->kill();
			return;
		}
		$this->getWorld()->addSound($target, new EndermanTeleportSound());
		$this->kill();
	}
}