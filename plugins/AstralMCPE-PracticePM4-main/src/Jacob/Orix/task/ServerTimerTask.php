<?php namespace Jacob\Orix\task;

use pocketmine\scheduler\Task;
use Jacob\Orix\AdvancedPractice;

class ServerTimerTask extends Task {

	private int $leaderboardUpdate = 0;

	public function onRun() : void {
		$this->leaderboardUpdate++;
		if ($this->leaderboardUpdate > 10) {
			$this->leaderboardUpdate = 0;
			AdvancedPractice::getLeaderboard()->updateLeaderboards();
		}
	}

}