<?php namespace Jacob\Orix\task;

use pocketmine\scheduler\Task;
use Jacob\Orix\AdvancedPractice;

class PlayerSecondTask extends Task {

	private int $second = 0;

	public function onRun() : void {
		$sessionMgr = AdvancedPractice::getSessionManager();
		foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
			$sessionMgr->getPlayerSession($player)->updateScoreboard();
			$sessionMgr->getPlayerSession($player)->tickCooldowns();
			$sessionMgr->getPlayerSession($player)->updateScoreTag();
			if ($this->second > 60) {
				$this->second = 0;
				$sessionMgr->getPlayerSession($player)->updateNameTag();
			}
		}
		$this->second++;
	}

}