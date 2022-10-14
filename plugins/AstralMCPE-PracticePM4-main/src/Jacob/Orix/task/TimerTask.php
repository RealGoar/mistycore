<?php
declare(strict_types=1);

namespace Jacob\Orix\task;

use pocketmine\scheduler\Task;
use pocketmine\utils\MainLogger;
use Jacob\Orix\AdvancedPractice;

class TimerTask extends Task {

  /** @var AdvancedPractice $plugin */
  private $plugin;

  public function __construct(AdvancedPractice $plugin) {
    $this->plugin = $plugin;
  }
    
  public function onRun():void {
    $this->getPlugin()->getTournment()->tick();
    if($this->getPlugin()->getTournment()->isIdle()) {
      $this->getHandler()->cancel();
    }
  }
    
  public function getPlugin(): AdvancedPractice {
    return $this->plugin;
  }
}