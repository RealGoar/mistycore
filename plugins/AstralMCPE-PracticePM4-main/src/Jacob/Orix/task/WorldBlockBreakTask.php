<?php


namespace Jacob\Orix\task;


use Jacob\Orix\AdvancedPractice;
use pocketmine\block\Block;
use pocketmine\block\Concrete;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;
use pocketmine\scheduler\Task;
use pocketmine\world\World;

class WorldBlockBreakTask extends Task
{
        public function __construct(
        public Block $block,
    ){}
    
    public array $blocks = [];

    public function onRun(): void
    {
        $block = $this->block;
        $delay = 10;
        $this->blocks[$block->getPosition()->getX() . ':' . $block->getPosition()->getY() . ':' . $block->getPosition()->getZ()] = time() + $delay;
        foreach ($this->blocks as $posString => $time) {
            AdvancedPractice::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function (): void {
                foreach ($this->blocks as $posString => $time) {
                    $pos = explode(':', $posString);
                    $world = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("builduhc");
                    $position = new Position($pos[0], $pos[1], $pos[2], $world);
                    $world->setBlock($position, VanillaBlocks::AIR());
                }
            }));
            }
    }
}
