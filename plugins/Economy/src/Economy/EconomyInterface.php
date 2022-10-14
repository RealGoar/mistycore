<?php

namespace Economy;

use Economy\Capital\CapitalInterface;

interface EconomyInterface {

	public function get(string $capital): CapitalInterface;

	public function remove(string $capital): void;

	public function clearMemory(): void;

	public function getName(): string;
}