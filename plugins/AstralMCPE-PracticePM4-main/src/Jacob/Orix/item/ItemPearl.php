<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 8/1/2022
 *
 * Copyright © 2021 zOmArRD - All Rights Reserved.
 */
declare(strict_types=1);

namespace Jacob\Orix\item;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ProjectileItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use Jacob\Orix\entity\PearlEntity;

final class ItemPearl extends ProjectileItem
{
	/**
	 * @param Int $meta
	 */
	public function __construct($meta = 0)
	{
		parent::__construct(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), "Ender Pearl");
	}

	/**
	 * @return Int
	 */
	public function getMaxStackSize(): int
	{
		return 16;
	}

	/**
	 * @return String
	 */
	public function getProjectileEntityType(): string
	{
		return "EnderPearl";
	}

	/**
	 * @return float
	 */
	public function getThrowForce(): float
	{
		return 2.1;
	}

	protected function createEntity(Location $location, Player $thrower): Throwable
	{
		$nbt = new CompoundTag();
		$nbt->setTag('Pos', new ListTag([
			new DoubleTag($thrower->getPosition()->x),
			new DoubleTag($thrower->getPosition()->y + $thrower->getEyeHeight()),
			new DoubleTag($thrower->getPosition()->z),
		]));
		$nbt->setTag('Motion', new ListTag([
			new DoubleTag(-sin($thrower->getLocation()->yaw / 180 * M_PI) * cos($thrower->getLocation()->pitch / 180 * M_PI)),
			new DoubleTag(-sin($thrower->getLocation()->pitch / 180 * M_PI)),
			new DoubleTag(cos($thrower->getLocation()->yaw / 180 * M_PI) * cos($thrower->getLocation()->pitch / 180 * M_PI)),
		]));
		$nbt->setTag('Rotation', new ListTag([
			new DoubleTag($thrower->getLocation()->yaw),
			new DoubleTag($thrower->getLocation()->pitch),
		]));
		$entity = new PearlEntity($location, $thrower, $nbt);
		$entity->setMotion($entity->getMotion()->multiply($thrower->getInventory()->getItemInHand()->getThrowForce()));
		return $entity;
	}
}