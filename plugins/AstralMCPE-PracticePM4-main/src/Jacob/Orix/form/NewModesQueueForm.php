<?php


namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use cosmicpe\form\types\Icon;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\duel\Queue;

class NewModesQueueForm extends SimpleForm {

    public function __construct(bool $isRanked = true) {
        parent::__construct(($isRanked ? "Ranked" : "Un-Ranked")." Queue", "Tap a mode to queue for.");
        foreach (AdvancedPractice::getInstance()->newModes as $name => $modeInfo) {
            $queued = false;
            foreach (AdvancedPractice::getInstance()->queues as $class) {
                if ($class instanceof Queue) {
                    if ($class->isRanked() == $isRanked and $class->getMode() == $name) {
                        $queued = true;
                    }
                }
            }
            //Because if someones queued, its never gonna go over two. This reason being that once theirs two it makes duel lol.
            $this->addButton(new Button(($isRanked ? "Ranked " : "Un-Ranked ").$name."\nQueued: ".($queued ? "1" : "0"), new Icon(Icon::PATH, $modeInfo["icon"])), function(Player $player, int $index) use ($isRanked, $name) {
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->createQueue($name, true);
            });
        }
    }

}