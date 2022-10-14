<?php namespace Jacob\Orix\duel;

use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use Jacob\Orix\AdvancedPractice;

class Queue {

	private bool $ranked = false;

	private string $mode = "";

	private Player $player;

	public function __construct(string $mode, bool $ranked, Player $player) {
		$this->player = $player;
		foreach (AdvancedPractice::getInstance()->queues as $class) {
			if ($class instanceof Queue) {
				if ($class->getPlayer()->getName() == $player->getName()) continue;
				if ($class->getMode() == $mode and $class->isRanked() == $ranked) {
					$p = $class->getPlayer();
					new Duel($ranked, $mode, $p, $player);
					$this->end();
					AdvancedPractice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($p) : void {
						AdvancedPractice::getSessionManager()->getPlayerSession($p)->endQueue();
					}), 10);
					return;
				}
			}
		}
		$this->mode = $mode;
		$this->ranked = $ranked;
	}

	public function isRanked() : bool {
		return $this->ranked;
	}

	public function getMode() : string {
		return $this->mode;
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function end() {
		AdvancedPractice::getSessionManager()->getPlayerSession($this->getPlayer())->endQueue();
	}

}