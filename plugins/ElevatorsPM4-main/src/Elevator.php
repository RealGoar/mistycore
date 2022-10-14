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

namespace zOmArRD\elevators;

use pocketmine\plugin\PluginBase;
use zOmArRD\elevators\config\Config;
use zOmArRD\elevators\listener\ElevatorListener;

final class Elevator extends PluginBase
{
	public static Elevator $instance;
	public static \AttachableLogger $logger;
	public static string $up, $down, $first;

	public static function getInstance(): Elevator
	{
		return self::$instance;
	}

	protected function onLoad(): void
	{
		self::$instance = $this;
		self::$logger = $this->getLogger();

		new Config();
	}

	protected function onEnable(): void
	{
		new ElevatorListener($this);

		$actions = explode(":", strtolower($this->getSignLines()["second"]));

		self::$first = strtolower($this->getSignLines()["first"]);
		self::$up = $actions[0];
		self::$down = $actions[1];

		self::$logger->info("§a". "Elevators by zOmArRD :)");
	}

	public function getSignLines(): array
	{
		$file = Config::getPluginConfig()->get('sign-lines');

		return [
			"first" => $file["first"],
			"second" => $file["second"]
		];
	}
}