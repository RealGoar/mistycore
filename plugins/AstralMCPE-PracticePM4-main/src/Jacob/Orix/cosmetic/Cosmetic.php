<?php namespace Jacob\Orix\cosmetic;

use Jacob\Orix\cosmetic\types\wp\Fire;

class Cosmetic {

	public array $cosmetics = [
		"Walking Particles" => []
	];

	public function init() : void {
		$this->cosmetics["Walking Particles"] = [
			new Fire()
		];
	}

}