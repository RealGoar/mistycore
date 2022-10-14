<?php

declare(strict_types = 1);

namespace Economy;

use Economy\EconomyInterface;
use Economy\Capital\SimpleCapital;
use Economy\Capital\CapitalInterface;
use Economy\Generic\Utils;

class SimpleEconomy implements EconomyInterface {

	private $path;
	private $defaultConfig = [
		'money' => 0,
		'type' => 'usd'
	];

	public function __construct() {
		$this->path = Utils::getDataFolder() . $this->getName() . '/';
		if (!is_dir($this->path)) {
			@mkdir($this->path, 777);
		}
	}

	public function get(string $capital): CapitalInterface {
		if (isset($this->storage[$capital])) {
			return $this->storage[$capital];
		}

		return $this->storage[$capital] = new SimpleCapital($capital, $this->path, $this->defaultConfig);
	}

	public function remove(string $capital): void {
		$this->get($capital)->remove();
		unset($this->capitals[$capital]);
	}

	public function clearMemory(): void {
		if (count($this->storage) == 0) {
			return;
		}

		foreach ($this->storage as $capital) {
			if (!$capital->canClearMemory()) {
				return;
			}

			if (round(microtime(true) - $capital->getLastTimeUsed()) > 60 * 60 * 2) {
				$capital->clearMemory();
			}
		}
	}

	public function getName(): string {
		return 'simple_economy';
	}
}