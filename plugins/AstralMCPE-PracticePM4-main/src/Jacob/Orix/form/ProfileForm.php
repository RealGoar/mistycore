<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use pocketmine\player\Player;

class ProfileForm extends SimpleForm {

	public function __construct() {
		parent::__construct("Your Profile", "Choose a option.");
		$this->addButton(new Button("Stats\nClick Me!"), function(Player $player, int $index) {
			$player->sendForm(new ProfileStatsForm($player->getName()));
		});
		$this->addButton(new Button("Settings\nClick Me!"), function(Player $player, int $index) {
			$player->sendForm(new SettingsForm($player));
		});
	}

}