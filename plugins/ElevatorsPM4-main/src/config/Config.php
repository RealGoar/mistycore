<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 18/1/2022
 *
 * Copyright © 2021 <zOmArRD@ghostlymc.live> - All Rights Reserved.
 */
declare(strict_types=1);

namespace zOmArRD\elevators\config;

use pocketmine\utils\Config as PMConfig;
use zOmArRD\elevators\Elevator;

final class Config
{
	public static PMConfig $plugin_config;
	private static Config $instance;
	private array $files = [
		'config.json' => 1.0
	];

	public function __construct()
	{
		self::$instance = $this;
		$this->init();
	}

	public function init(): void
	{
		/** Can this be removed? */
		if (!@mkdir($patch = $this->getDataFolder()) && !is_dir($patch)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $patch));
		}

		foreach ($this->files as $file => $version) {
			$this->saveResource($file);

			if ($this->getFile($file)->get('version') !== $version) {
				Elevator::$logger->error("The $file aren't compatible with the current version, the old file are in " . $this->getDataFolder() . "$file.old");
				rename($this->getDataFolder() . $file, $this->getDataFolder() . $file . ".old");
				$this->saveResource($file, true);
			}
		}

		self::$plugin_config = $this->getFile('config.json');
	}

	public function getDataFolder(): string
	{
		return Elevator::getInstance()->getDataFolder();
	}

	public function saveResource(string $file, bool $replace = false): void
	{
		Elevator::getInstance()->saveResource($file, $replace);
	}

	public function getFile(string $file): PMConfig
	{
		return new PMConfig($this->getDataFolder() . $file);
	}

	public static function getInstance(): Config
	{
		return self::$instance;
	}

	public static function getPluginConfig(): PMConfig
	{
		return self::$plugin_config;
	}
}