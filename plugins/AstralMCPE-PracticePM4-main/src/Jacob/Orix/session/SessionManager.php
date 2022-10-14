<?php namespace Jacob\Orix\session;

use pocketmine\player\Player;

class SessionManager {

	/**
	 * @var array<string, PlayerSession>
	 */
	private array $playerSessions = [];

	/**
	 * @param Player $player
	 */
	public function createSession(Player $player) : void {
		$this->playerSessions[$player->getName()] = new PlayerSession($player);
	}

	/**
	 * @param Player $player
	 * @return PlayerSession
	 */
	public function getPlayerSession(Player $player) : PlayerSession {
		return $this->playerSessions[$player->getName()];
	}

	/**
	 * @param Player $player
	 */
    public function closeSession(Player $player) : void {
        if(isset($this->playerSessions[$player->getName()]) === true) {
            $this->getPlayerSession($player)->close();
            unset($this->playerSessions[$player->getName()]);
        }
    }

}
