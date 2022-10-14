<?php namespace Jacob\Orix\form;

use cosmicpe\form\CustomForm;
use cosmicpe\form\entries\custom\InputEntry;
use cosmicpe\form\entries\custom\ToggleEntry;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use function in_array;

class SettingsForm extends CustomForm {

	private array $settings = [];

    public function __construct(Player $player) {
        parent::__construct("Your Settings");
        $currentSettings = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getFullPlayerData()["settings"];
        $this->settings = $currentSettings;
        $sprint = new ToggleEntry("Auto-Sprint\nDescription: Automatically sprint without pressing anything.", $currentSettings["sprint"]);
        $this->addEntry($sprint, function (Player $player, ToggleEntry $entry, bool $input) {
            $this->settings["sprint"] = $input;
        });
        $sprint = new ToggleEntry("Auto-Rekit\nDescription: Automatically rekit after each kill.", $currentSettings["rekit"]);
        $this->addEntry($sprint, function (Player $player, ToggleEntry $entry, bool $input) {
            $this->settings["rekit"] = $input;
        });
        if($player->hasPermission("hearts.settings")) {
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->updateNameTag();
            $hearts = new ToggleEntry("Toggle Hearts\nDescription: These hearts are displayed everybody and shows the health they are at.", $currentSettings["health"]);
            $this->addEntry($hearts, function (Player $player, ToggleEntry $entry, bool $input) {
                $this->settings["health"] = $input;
            });
        }
        $cps = new ToggleEntry("Show CPS\nDescription: Show your Clicks per Second.", $currentSettings["cps"]);
        $this->addEntry($cps, function(Player $player, ToggleEntry $entry, bool $input) {
            $this->settings["cps"] = $input;
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setSettings($this->settings);
            $player->sendMessage("§dUpdated settings.");
        });
    }

}