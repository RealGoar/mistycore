<?php

declare(strict_types = 1);

namespace Economy\Generic;

use Economy\Generic\Utils;

use pocketmine\utils\Config;

class SimpleConfig {

	protected $config;
	private $defaultConfig = [];
	private $path = null;

	public function __construct(string $path, $defaultConfig) {
		$this->defaultConfig = $defaultConfig;
		$this->makeConfig($path);
	}

	public function get($key) {
		$this->check();
		return $this->config->get($key, null);
	}

	public function set($key, $value) {
		$this->check();
		$this->config->set($key, $value);
		$this->config->save();
	}

	public function remove($key) {
		$this->check();
		$this->config->remove($key);
		$this->config->save();
	}

	public function clear(): void {
		$this->config = null;
	}

	private function check(): void {
		if ($this->config !== null) {
			return;
		}

		$this->makeConfig($this->path);
	}

	private function makeConfig(string $path) {
		if ($this->path === null) {
			$this->path = str_replace(Utils::getDataFolder(), '', str_replace('.yml', '', $path));
		}
		$this->config = new Config(Utils::getDataFolder() . $this->path . '.yml', Config::YAML, $this->defaultConfig);
	}
}