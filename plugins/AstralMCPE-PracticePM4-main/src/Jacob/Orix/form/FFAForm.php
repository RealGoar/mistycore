<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use cosmicpe\form\types\Icon;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Jacob\Orix\AdvancedPractice;
use function strtolower;

class FFAForm extends SimpleForm {

	public function __construct() {
		parent::__construct("Free For All", "Choose a Arena to teleport to!");
		foreach (AdvancedPractice::getInstance()->modes as $name => $info) {

            if ($name === "Gapple") {
                return;
            } else {
                if ($name === "Soup"){
                    return;
                } else {
                if ($name === "BaseRaiding") {
                    return;
                } else {
                    if ($name === "SafeRoom"){
                        return;
                    } else {
                        $this->addButton(new Button($name . "\nPlaying: " . (count(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName(strtolower($name))->getPlayers())), new Icon(Icon::PATH, $info["icon"])), function (Player $player, int $index) use ($name) {
                            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setInFFA($name);
                        });
                    }
                }
            }
        }
    }
}
	

}