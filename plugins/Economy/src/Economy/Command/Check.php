<?php

declare(strict_types = 1);

namespace Economy\Command;

use Economy\Factory;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Check extends Command {

	public function __construct($commandMap) {
		parent::__construct('checkmoney', 'check money', '/checkmoney (optional | player)', ['checkcoins']);
		$commandMap->register('/check', $this);
	}

	public function execute(CommandSender $sender, string $label, array $args) {
		if (isset($args[0]) && (!is_string($args[0]))) {
			$sender->sendMessage('§7enter a valid name');
			return;
		}

		if (!isset($args[0])) {
			if (!$sender instanceof Player) {
				$sender->sendMessage('§7use /' . $label . ' (player)');
				return;
			}

			$sender->sendMessage('§fyour coins: ' . $this->check($sender->getName()));
			return;
		}

		$sender->sendMessage('§f' . $args[0] . ' coins: ' . $this->check((string)$args[0]));
	}

	public function check(string $playerName): int {
		return Factory::getInstance()->get('simple_economy')->get($playerName)->get();
	}
}