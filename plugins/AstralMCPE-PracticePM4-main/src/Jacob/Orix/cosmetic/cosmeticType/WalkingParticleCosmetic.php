<?php namespace Jacob\Orix\cosmetic\cosmeticType;

use pocketmine\world\particle\Particle;

abstract class WalkingParticleCosmetic extends CosmeticType {

	abstract function getParticleType() : Particle;

}