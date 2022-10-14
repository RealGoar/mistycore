<?php

namespace itoozh\NPCSystem\Command;

use itoozh\NPCSystem\Entity\EventsNPCEntity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class NPCCommand extends Command
{
    public function __construct()
    {
        parent::__construct("npc", "Plugin by itoozh");
        $this->setPermission('npc.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return;
        }
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§r§c/npc (npc name)');
            return;
        }
        if (strtolower($args[0]) === 'events') {
            if (!isset($args[1])) {
                $entity = EventsNPCEntity::create($sender);
                $entity->spawnToAll();
                $sender->sendMessage(TextFormat::colorize('§r§aNPC created successfully!'));
            }
        }
    }
}