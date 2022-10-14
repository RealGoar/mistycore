<?php


namespace Jacob\Orix\task;


use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class BlockBreakTask extends Task
{
    public function __construct(
        public Block $block,
        public $time = 30,
    ){}

    public function onRun(): void
    {
        if ($this->time !== 0) {
            --$this->time;
        }
        if($this->time === 15) {
            $b = $this->block;
            $b->getPosition()->getWorld()->setBlock($b->getPosition() , VanillaBlocks::COBBLESTONE());
        }
        if($this->time <= 0){
            //    $this->player->setLastDamageCause(null);
            $this->block->getPosition()->getWorld()->setBlock($this->block->getPosition(), VanillaBlocks::AIR());
            $this->getHandler()->cancel();
        }
}
}