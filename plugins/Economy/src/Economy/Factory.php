<?php

declare(strict_types = 1);

namespace Economy;

use Economy\EconomyInterface;
use Economy\Exception\EconomyNotFound;
use Economy\Exception\EconomyAlreadyExists;

use pocketmine\utils\SingletonTrait;
use pocketmine\event\Listener;
use pocketmine\event\server\LowMemoryEvent;

final class Factory implements Listener {

	use SingletonTrait;

	private $economies = [];

	private function __construct() {
		$this->register(new SimpleEconomy());
	}

	public static function init(): void {
		self::setInstance(new Self());
	}

	public function register(EconomyInterface $economy): void {
		if (isset($this->economies[$economy->getName()])) {
			throw new EconomyAlreadyExists("the '" . $economy->getName() . "' economy already exists");
		}

		$this->economies[$economy->getName()] = $economy;
	}

	public function get(string $economy): EconomyInterface {
		if (!isset($this->economies[$economy])) {
			throw new EconomyNotFound("the '" . $economy . "' economy not found");
		}

		return $this->economies[$economy];
	}

	public function onLowMemory(LowMemoryEvent $event) {
		if (count($this->economies) == 0) {
			return;
		}

		foreach ($this->economies as $economy) {
			$economy->clearMemory();
		}
	}

	public function registerListener(): void {
		Main::getInstance()->getServer()->getPluginManager()->registerEvents($this, Main::getInstance());
	}
}