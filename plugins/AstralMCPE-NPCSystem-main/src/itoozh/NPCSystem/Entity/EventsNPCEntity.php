<?php

namespace itoozh\NPCSystem\Entity;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EventsNPCEntity extends Human
{

    /** @var int|null */

    /**
     * @param Player $player
     *
     * @return EventsNPCEntity
     */
    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     */
    public function onUpdate(int $currentTick): bool
    {
        $parent = parent::onUpdate($currentTick);

        $this->setNameTag(TextFormat::colorize("§l§dHost events\n§r§eDonators have access to host!§r\n \n§ePurchase rank at §dstore.mistynetwork.xyz\n "));
        $this->setNameTagAlwaysVisible(true);

        return $parent;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if (!$source instanceof EntityDamageByEntityEvent) {
            return;
        }

        $damager = $source->getDamager();

        if (!$damager instanceof Player) {
            return;
        }

        if ($damager->getInventory()->getItemInHand()->getId() === 276) {
            if ($damager->hasPermission('removenpc.permission')) {
                $this->kill();
            }
            return;
        }


        $menu = InvMenu::create(InvMenuTypeIds::TYPE_HOPPER);
        $menu->setName("§r§a Host Events");
        $menu->getInventory()->setContents([
            0 => VanillaItems::SLIMEBALL()->setCustomName("§r§cSumo")->setLore(["§r§7Knock the people ogg the Sumo platform."]),
        ]);

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            if($transaction->getItemClicked()->getCustomName() === "§r§cSumo"){
                $player->getServer()->dispatchCommand($player, "event create Sumo");
                $player->removeCurrentWindow();
            }
            return $transaction->discard();
        });
        $menu->send($damager);
    }
}