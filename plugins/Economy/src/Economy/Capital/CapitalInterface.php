<?php

namespace Economy\Capital;

interface CapitalInterface {

	public function getName(): string;

	public function get();

	public function add(int $money);

	public function reduce(int $money): void;

	public function clearMemory(): void;

	public function getType(): string;
}