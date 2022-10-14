<?php namespace Jacob\Orix\cosmetic\types\wp;

use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\Particle;
use Jacob\Orix\cosmetic\cosmeticType\WalkingParticleCosmetic;

class Fire extends WalkingParticleCosmetic {


	function getParticleType() : Particle {
		return new FlameParticle();
	}

	function getName() : string {
		return "FireParticle";
	}

	function isKillsNeeded() : bool {
		return true;
	}

	function isPermissionNeeded() : bool {
		return false;
	}

	function getPermission() : string {
		return "un-needed";
	}

	function getKillsNeeded() : int {
		return 50;
	}

}