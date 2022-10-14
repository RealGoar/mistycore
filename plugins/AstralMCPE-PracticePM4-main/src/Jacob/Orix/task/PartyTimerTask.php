<?php
declare(strict_types=1);

namespace Jacob\Orix\task;

use Jacob\Orix\event\Event;
use pocketmine\scheduler\Task;
use pocketmine\utils\MainLogger;
use Jacob\Orix\AdvancedPractice;

class PartyTimerTask extends Task {

    /** @var AdvancedPractice $plugin */
    private AdvancedPractice $plugin;
    /** @var Event */
    private Event $event;

    public function __construct(Event $event, AdvancedPractice $plugin) {
        $this->event = $event;
        $this->plugin = $plugin;
    }

    public function onRun():void {
        $this->event->tick();
        if($this->event->isIdle()) {
            $this->getHandler()->cancel();
        }
    }

    public function getPlugin(): AdvancedPractice {
        return $this->plugin;
    }
}