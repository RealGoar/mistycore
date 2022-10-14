<?php

namespace itoozh\NPCSystem;

use itoozh\NPCSystem\Command\NPCCommand;
use itoozh\NPCSystem\Entity\EventsNPCEntity;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class Main extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {

        $this->saveResource("config.yml");

        $this->getServer()->getCommandMap()->register("npc", new NPCCommand());

        EntityFactory::getInstance()->register(EventsNPCEntity::class, function (World $world, CompoundTag $nbt): EventsNPCEntity {
            return new EventsNPCEntity(EntityDataHelper::parseLocation($nbt, $world), EventsNPCEntity::parseSkinNBT($nbt), $nbt);
        }, ['AbilityNPCEntity']);

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

    }

}