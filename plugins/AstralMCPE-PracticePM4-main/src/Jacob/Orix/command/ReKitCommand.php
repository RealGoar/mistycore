<?php namespace Jacob\Orix\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use pocketmine\utils\TextFormat;
use function array_shift;
use function in_array;
use function is_null;
use function strtolower;
use function time;

class ReKitCommand extends Command {

	private array $cooldown = [];

	public function __construct() {
		parent::__construct("rekit", "Re-Kit in FFA!");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if ($sender instanceof Player) {
			if (!isset($this->cooldown[$sender->getName()])) $this->cooldown[$sender->getName()] = time();
			else if (time() - $this->cooldown[$sender->getName()] < 30) {
				$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "This command is still on cooldown!");
				return;
			}
			$types = [];
			foreach (AdvancedPractice::getInstance()->modes as $name => $i) {
				$types[] = strtolower($name);
			}
			$world = strtolower($sender->getWorld()->getFolderName());
			if (!in_array($world, $types)) {
				$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot rekit in this world!");
				return;
			}
			AdvancedPractice::getSessionManager()->getPlayerSession($sender)->giveStringedKit($world);
			$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Successfully re-kitted.");
			$this->cooldown[$sender->getName()] = time();
		}
	}

}