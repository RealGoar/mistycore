<?php

declare(strict_types = 1);

namespace Respawn\Utils;

use Respawn\Main;
use Respawn\Respawn;
use pocketmine\scheduler\Task;

class Scheduler extends Task {

    private $respawn;

    public function __construct(Respawn $respswn) {
        $this->respawn = $respswn;
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function onRun(): void {
        $this->respawn->baseTick();
    }
    
    public function cancel() {
        $this->getHandler()->cancel();
    }
}