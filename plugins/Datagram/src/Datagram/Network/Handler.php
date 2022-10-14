<?php

declare(strict_types = 1);

namespace Datagram\Network;

use Datagram\IO\ThreadChannelWriter;
use Datagram\IO\ThreadChannelReader;

use raklib\generic\Socket;

final class Handler {

	/**
	* external message handler
	*
	* @return void
	* @var Socket $socket
	* @var ChannelWriter $channel
	*/
	public static function receiveData(Socket $socket, ThreadChannelWriter $channel): void {
		if (($buffer = $socket->readPacket($ip, $port)) === null) {
			return;
		}
		
		if ($buffer === null || strlen($buffer) == 0) {
			return;
		}

		$channel->write($ip . ':' . $port . "%20" . $buffer);
	}

	/**
	* internal message handler
	*
	* @return void
	* @param Socket $socket
	* @param ThreadChannelReader $channel
	*/
	public static function sendData(Socket $socket, ThreadChannelReader $channel): void {
		if (($data = $channel->read()) !== null) {
			$content = explode('%20', $data);
			if (count($content) < 2) {
				return;
			}

			$address = explode(':', $content[0]);
			$socket->writePacket($content[1], (string)$address[0], (int)$address[1]);
		};
	}
}