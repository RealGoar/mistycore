<?php

declare(strict_types = 1);

namespace Datagram\IO;

use Threaded;

final class MainChannelWriter {

	public function __construct(private Threaded $buffer) {}

	public function write(string $buffer) {
		$this->buffer[] = $buffer;
	}
}