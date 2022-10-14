<?php

declare(strict_types = 1);

namespace Datagram;

use UDPServer;

interface SubscriberInterface {

	/**
	* called when the subscriber receive a message
	*
	* @return void
	* @param string|mixed $buffer
	* @param string $ip
	* @param int $port
	*/
	public function onReceive($data, string $ip, int $port, UDPServer $server): void;
}