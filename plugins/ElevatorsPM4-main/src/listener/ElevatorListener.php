<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 18/1/2022
 *
 * Copyright © 2021 <zOmArRD@ghostlymc.live> - All Rights Reserved.
 */
declare(strict_types=1);

namespace zOmArRD\elevators\listener;

use pocketmine\block\Air;
use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use zOmArRD\elevators\config\Config;
use zOmArRD\elevators\Elevator;

final class ElevatorListener implements Listener
{
	public function __construct(private Elevator $elevator)
	{
		$elevator->getServer()->getPluginManager()->registerEvents($this, $elevator);
	}

	public function getElevator(): Elevator
	{
		return $this->elevator;
	}

	public function SignChangeEvent(SignChangeEvent $event): void
	{
		$player = $event->getPlayer();
		$text = $event->getNewText();

		if ($text->getLine(0) === '' || is_null($text->getLine(0))) {
			return;
		}

		if (strtolower($text->getLine(0)) === Elevator::$first) {
			if (strtolower($text->getLine(1)) === Elevator::$up) {
				$event->setNewText(new SignText(["§6[Elevator]", "§a" . Elevator::$up]));
				$player->sendMessage($this->getMessages()["elevator-create-success"]);
				return;
			}

			if (strtolower($text->getLine(1)) === Elevator::$down) {
				$event->setNewText(new SignText(["§6[Elevator]", "§a" . Elevator::$down]));
				$player->sendMessage($this->getMessages()["elevator-create-success"]);
				return;
			}

			$event->setNewText(new SignText(["§6[Elevator]", "§cError"]));
			$player->sendMessage($this->getMessages()["elevator-create-fail"]);
		}
	}

	public function getMessages(): array
	{
		return Config::getPluginConfig()->get('messages');
	}

	public function PlayerInteractEvent(PlayerInteractEvent $event): void
	{
		$player = $event->getPlayer();

		if ($event->getAction() !== $event::RIGHT_CLICK_BLOCK) {
			return;
		}

		$block = $event->getBlock();

		if (!$block instanceof BaseSign) {
			return;
		}

		$text = $block->getText();
		if ($text->getLine(0) === "§6[Elevator]") {
			$event->cancel();
			switch ($text->getLine(1)) {
				case "§a" . Elevator::$up:
					$this->elevatePlayer($player, $block->getPosition());
					$player->sendMessage($this->getMessages()["elevator-teleport-up"]);
					break;
				case "§a" . Elevator::$down:
					$this->elevatePlayer($player, $block->getPosition(), "down");
					$player->sendMessage($this->getMessages()["elevator-teleport-down"]);
					break;
			}
		}
	}

	public function elevatePlayer(Player $player, Position $position, string $mode = "up"): void
	{
		$x = $position->getFloorX();
		$y = $position->getFloorY();
		$z = $position->getFloorZ();

		if ($mode === "down") {
			for ($i = $y - 1; $i >= 0; $i--) {
				$pos1 = $position->getWorld()->getBlockAt($x, $i, $z);
				$pos2 = $position->getWorld()->getBlockAt($x, $i + 1, $z);

				if (!$pos1 instanceof Air || !$pos2 instanceof Air) {
					continue;
				}

				$player->teleport(self::getCenterBlock(new Vector3($x, $i, $z)));
				break;
			}
		}

		if ($mode === "up") {
			$level = $position->getWorld();
			for ($i = $y + 1; $i <= 256; $i++) {
				$pos1 = $level->getBlockAt($position->getFloorX(), $i, $position->getFloorZ());
				$pos2 = $level->getBlockAt($position->getFloorX(), $i + 1, $position->getFloorZ());
				$pos3 = $level->getBlockAt($position->getFloorX(), $i - 1, $position->getFloorZ());

				if (!$pos1 instanceof Air || !$pos2 instanceof Air || $pos3 instanceof Air) {
					continue;
				}

				$player->teleport(self::getCenterBlock(new Vector3($position->getFloorX(), $i, $position->getFloorZ())));
				break;
			}
		}
	}

	private static function getCenterBlock(Vector3 $vector3): Vector3
	{
		return $vector3->add(0.5, 0, 0.5);
	}
}