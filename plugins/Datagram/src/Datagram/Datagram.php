<?php

declare(strict_types = 1);

namespace Datagram;

use Datagram\SubscriberInterface;

use Evenement\EventEmitter;

use pocketmine\utils\SingletonTrait;

class Datagram extends EventEmitter {

	use SingletonTrait;

	private $subscribers = [];

	public function __construct(private UDPServer $server) {
		self::setInstance($this);
		$this->on('data', function ($data, $ip, $port) {
			$address = $ip . ':' . $port;
			if (!isset($this->subscribers[$address])) {
				return;
			}

			foreach ($this->getSubscribers($address) as $subscriber) {
				$subscriber->onReceive($data, $ip, (int)$port, $server);
			}
		});
	}

	public function getServer(): UDPServer {
		return $this->server;
	}

	/**
	* subscribe to receive messages from the server
	*
	* @return void
	* @param InternetAddress $address;
	* @param SubscriberInterface $subscriber
	*/
	public function subscribe(string $ip,
		int $port,
		SubscriberInterface $subscriber
	) {
		$this->subscribers[$ip . ':' . $port][] = $subscriber;
	}

	private function getSubscribers(string $address) {
		foreach ($this->subscribers[$address] as $subscriber) {
			yield $subscriber;
		}
	}
}