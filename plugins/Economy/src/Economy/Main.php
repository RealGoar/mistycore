<?php

declare(strict_types = 1);

namespace Economy;

use Economy\Command\Add;
use Economy\Command\Remove;
use Economy\Command\Check;
use Economy\Factory;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

	use SingletonTrait;

	public function onLoad(): void {
		self::setInstance($this);
		Factory::init();
	}

	public function onEnable(): void {
		Factory::getInstance()->registerListener();

		// Commands
		$cmdMap = $this->getServer()->getCommandMap();
		new Add($cmdMap);
		new Remove($cmdMap);
		new Check($cmdMap);
	}
}