<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;

class ProfileStatsForm extends SimpleForm {

	public function __construct(string $name) {
		$p = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($name);
		if ($p !== null) {
			$list = AdvancedPractice::getSessionManager()->getPlayerSession($p)->getFullPlayerData();
		} else {
			$list = AdvancedPractice::getInstance()->playerDatabase->get($name);
		}
		$stats = [
			"§dYour Stats",
			"     ",
			"§dKills: §7".$list["kills"],
			"§dDeaths: §7".$list["deaths"],
			"§dKillstreak: §7".$list["killstreak"],
            "§dDuel Wins: §7".$list["wins"],
            "§dDuel Losses: §7".$list["losses"],
			"    ",
			"§dYour Arena Stats",
			"     ",
			"§dNoDebuff-ELO: §7".$list["elo"]["NoDebuff"],
            "§dGapple-ELO: §7".$list["elo"]["Gapple"],
            "§dCombo-ELO: §7".$list["elo"]["Combo"],
            "§dSumo-ELO: §7".$list["elo"]["Sumo"],
            "§dSoup-ELO: §7".$list["elo"]["Soup"],
            "§dAbility-ELO: §7".$list["elo"]["Ability"],
            "§dDragon-ELO: §7".$list["elo"]["Dragon"],
            "§dBase Raiding-ELO: §7".$list["elo"]["BaseRaiding"],
            "§dKnockback-ELO: §7".$list["elo"]["Knockback"],
            "§dSafeRoom-ELO: §7".$list["elo"]["SafeRoom"],
            "§dFist-ELO: §7".$list["elo"]["Fist"],
		];
		parent::__construct($name."'s Statistics", implode("\n", $stats));
		$this->addButton(new Button("§4Close"), function(Player $player, int $index) {});
	}

}