<?php

declare(strict_types = 1);

namespace Economy\Capital;

use Economy\Generic\SimpleConfig;
use Economy\Capital\CapitalInterface;

class SimpleCapital implements CapitalInterface {

	private $name;

	private $lastTimeUsed;
	private $cleaned = false;

	private $config;
	private $defaultConfig;

	public function __construct(string $name, $path, array $defaultConfig = []) {
		$this->name = $name;
		$this->defaultConfig = $defaultConfig;
		$this->config = new SimpleConfig($path . $name, $defaultConfig);
		$this->lastTimeUsed = microtime(true);
	}

	public function getName(): string {
		return $this->name;
	}

	public function get() {
		$this->check();
		return $this->config->get('money', 0);
	}

	public function add(int $money) {
		$this->check();
		$this->config->set('money', $this->get() + $money);
	}

	public function reduce(int $money): void {
		$this->check();
		$total = $this->get() - $money;
		if ($total < 0) {
			$total = $this->get();
		}
		$this->config->set('money', $total);
	}

	public function getType(): string {
		return $this->config->get('type', $this->defaultConfig['type']);
	}

	public function clearMemory(): void {
		$this->config->clear();
		$this->cleaned = true;
	}

	public function canClearMemory(): bool {
		return $this->cleaned == false;
	}

	public function getLastTimeUsed() {
		return $this->getLastTimeUsed;
	}

	public function check() {
		$this->lastTimeUsed = microtime(true);
		if ($this->cleaned === false) {
			return;
		}

		$this->cleaned = false;
	}
}