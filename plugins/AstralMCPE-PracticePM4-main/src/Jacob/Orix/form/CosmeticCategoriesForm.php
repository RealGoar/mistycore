<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;

class CosmeticCategoriesForm extends SimpleForm {

	public function __construct() {
		parent::__construct("Cosmetics", "Choose a category!");
		foreach (AdvancedPractice::getCosmetics()->cosmetics as $name => $list) {
			$this->addButton(new Button($name."\n".(count($list))." Options"), function(Player $player, int $index) use ($name) {
				$player->sendForm(new CosmeticListForm($name, $player));
			});
		}
	}

}