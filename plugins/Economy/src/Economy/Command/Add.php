<?php

declare(strict_types = 1);

namespace Economy\Command;

use Economy\Factory;

use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Add extends Command {

	public function __construct($commandMap) {
		parent::__construct('addmoney', 'add money', '/addmoney (amount) (optional | player)', ['addcoins']);
		$commandMap->register('/addmoney', $this);
	}

	public function execute(CommandSender $sender, string $label, array $args) {
		if ($sender instanceof Player) {
			if (!Server::getInstance()->isOp($sender->getName())) {
				$sender->sendMessage('§7You do not have permission');
				return;
			}
		}
		if (!isset($args[0])) {
			$sender->sendMessage('§7use /' . $label . ' (amount) (optio al | player)');
			return;
		}

		if ((!is_numeric($args[0])) || $args[0] == 0) {
			$sender->sendMessage('§7enter a valid amount');
			return;
		}

		if (!isset($args[1])) {
			if (!$sender instanceof Player) {
				$sender->sendMessage('§7use /' . $label . ' (amount) (player)');
				return;
			}

			$this->add((int)$args[0], $sender->getName());
			$sender->sendMessage('Successfully added ' . $args[0] . ' to your account');
			return;
		}

		$this->add((int)$args[0], (string)$args[1]);
		$sender->sendMessage('Successfully added ' . $args[0] . ' to ' . $args[1] . ' account');
	}

	public function add(int $amount, string $playerName) {
		Factory::getInstance()->get('simple_economy')->get($playerName)->add($amount);
	}
}