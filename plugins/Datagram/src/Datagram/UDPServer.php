<?php

declare(strict_types = 1);

namespace Datagram;

use Datagram\Datagram;
use Datagram\Handler;
use Datagram\Network\Network;
use Datagram\IO\MainChannelReader;
use Datagram\IO\MainChannelWriter;

use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\snooze\SleeperHandler;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Config;
use raklib\utils\InternetAddress;

use Threaded;
use const PTHREADS_INHERIT_NONE;

class UDPServer {

	use SingletonTrait;

	/** @var Network $network */
	private $network;

	/** @var InternetAddress */
	private $address;

	/** @var MainChannelWriter */
	private $channelWriter;

	/** @var SleeperNotifier */
	private $notifier;

	/** @var Handler */
	private $handler;

	private $running = true;

	private function __construct(string $ip, int $port, $version) {
		$this->notifier = new SleeperNotifier();
		$mainToThread = new Threaded();
		$threadToMain = new Threaded();

		$address = new InternetAddress($ip, $port, $version);

		$this->network = new Network(
			$address,
			$this->notifier,
			$mainToThread,
			$threadToMain
		);

		$channelReader = new MainChannelReader($threadToMain);
		$this->channelWriter = new MainChannelWriter($mainToThread);
		$datagram = new Datagram($this);
		$this->handler = new Handler($channelReader, $datagram);
		$this->run();
	}

	private function run() {
		Server::getInstance()->getTickSleeper()->addNotifier($this->notifier, function() : void {
			while ($this->handler->handle());
		});
		try {
			$this->network->start(PTHREADS_INHERIT_NONE);
		} catch (Exception) {
			echo PHP_EOL . $e->getMessage() . PHP_EOL;
		}
	}

	public function getAddress(): InternetAddress {
		return $this->address;
	}

	public function send($buffer,
		string $ip,
		int $port) {
		$this->channelWriter->write($ip . ':' . $port . '%20' . $buffer);
	}

	public static function init(string $path): void {
		if (self::$instance instanceof UDPServer) {
			return;
		}
		$config = new Config($path . 'config.yml', Config::YAML, [
			'server_ip' => '0.0.0.0',
			'ip_version' => 4,
			'server_port' => 19135
		]);
		self::setInstance(new Self($config->get('server_ip'), $config->get('server_port'), $config->get('ip_version')));
	}
}