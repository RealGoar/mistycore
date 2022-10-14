<?php

declare(strict_types = 1);

namespace Datagram;

use Datagram\UDPServer;
use Datagram\Generic\DatagramException;

use pocketmine\plugin\PluginBase;;

class Main extends PluginBase {

	public function onLoad(): void {
		$bootstrap = dirname(__DIR__, 2) . '/vendor/autoload.php';
		if (!is_file($bootstrap)) {
			throw new DatagramException('install the composer dependencies');
		}

		require_once($bootstrap);
		UDPServer::init($this->getDataFolder());
	}
}