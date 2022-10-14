<?php

namespace Jacob\Orix\punishments;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\Data\PlayerBase;
use Jacob\Orix\Data\Data;
use Jacob\Orix\Data\Country;
use Jacob\Orix\anticheat\AntiCheat;
use Jacob\Orix\anticheat\Alerts;
use Exception;
use pocketmine\block\PackedIce;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\LegacyEntityIdToStringIdMap;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use ReflectionClass;
use ReflectionProperty;
use Jacob\Orix\util\Time;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\world\Position;
use pocketmine\utils\TextFormat as TE;
use pocketmine\item\{Item, ItemIds, Potion, VanillaItems};

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;

use pocketmine\event\player\{PlayerItemUseEvent,
    PlayerJoinEvent,
    PlayerLoginEvent,
    PlayerQuitEvent,
    PlayerInteractEvent,
    PlayerChatEvent,
    PlayerDeathEvent,
    PlayerMoveEvent,
    PlayerPreLoginEvent,
    PlayerCommandPreprocessEvent};
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\event\block\{BlockBreakEvent, BlockPlaceEvent};

class EventListener implements Listener
{

    /** @var array[] */
    protected array $spam = [];

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoinEvent(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        PlayerBase::create($player->getName(), $player->getNetworkSession()->getIp(), $player->getXuid(), $player->getUniqueId()->toString(), /*PlayerBase::getDevice($player)*/ "Unknown");

        if (!PlayerBase::isEmptyPlayers()) {
            foreach (PlayerBase::getPlayersHide() as $session) {
                if (!$player->hasPermission("mod.command.use"))
                    $player->hidePlayer($session);
            }
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuitEvent(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        //This function is to remove the player from the array, whose name says something else xD
        PlayerBase::showPlayer($player);
        PlayerBase::removeStaff($player);
    }

    public function onUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        /*if ($item->getId() === ItemIds::SPLASH_POTION){
            if (PlayerBase::getDevice($player) === "Android") {
                $effects = PotionTypeIdMap::getInstance()->fromId($item->getMeta())->getEffects();
                foreach ($effects as $effect) {
                    $player->getEffects()->add($effect);
                }
                $event->cancel();
                if($player->isSurvival()){
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand(VanillaBlocks::AIR()->asItem());
                }

                $pk = new LevelSoundEventPacket();
                $pk->sound = 127;
                $pk->extraData = -1;
                $pk->entityType = LegacyEntityIdToStringIdMap::getInstance()->legacyToString(-1) ?? ":";
                $pk->isBabyMob = false;
                $pk->disableRelativeVolume = false;
                $pk->position = $player->getPosition()->asVector3();
            }
        }*/

        if (PlayerBase::isStaff($player)) {
            if ($item->getId() === ItemIds::CLOCK) {
                /** @var Player[] $players */
                $players = [];
                foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online) {
                    $players[] = $online;
                }
                $randomPlayer = $players[array_rand($players)];
                if ($player->getName() === $randomPlayer->getName()) return;

                $player->sendMessage(TE::GRAY . "Teleported to the player" . TE::WHITE . ": " . TE::LIGHT_PURPLE . $randomPlayer->getName());
                $player->teleport($randomPlayer->getPosition());
            }
            if ($item->getId() === ItemIds::COMPASS) {
                $direction = $player->getDirectionVector()->multiply(4);
                $player->teleport(Position::fromObject($event->getPlayer()->getPosition()->add($direction->getX(), $direction->getY(), $direction->getZ()), $event->getPlayer()->getWorld()));
            }
            if ($item->getId() === ItemIds::DYE && $item->getMeta() === 10) {
                $player->getInventory()->setItemInHand(VanillaItems::RED_DYE()->setCustomName(TE::LIGHT_PURPLE . "Disable Vanish"));
                $player->sendMessage(TE::GREEN . "Vanish activated!");

                PlayerBase::hidePlayer($player);
                foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online) {
                    if (!$online->hasPermission("mod.command.use")) {
                        $online->hidePlayer($player);
                    }
                }
            }
            if ($item->getId() === ItemIds::DYE && $item->getMeta() === 1) {
                $player->getInventory()->setItemInHand(VanillaItems::LIME_DYE()->setCustomName(TE::GREEN . "Enable Vanish"));
                $player->sendMessage(TE::LIGHT_PURPLE . "Vanish deactivated!");

                PlayerBase::showPlayer($player);
                foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $online) {
                    if (!$online->hasPermission("mod.command.use")) {
                        $online->showPlayer($player);
                    }
                }
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onEntityDamageEvent(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($player instanceof Player && $damager instanceof Player) {
                if (AntiCheat::haveReach($damager, $player)) {
                    Alerts::send($damager, "reach");
                }
                if (PlayerBase::isStaff($damager)) {
                    echo "a";
                    $item = $damager->getInventory()->getItemInHand();
                    $ice = VanillaBlocks::PACKED_ICE()->asItem();
                    if ($item instanceof $ice) {
                        echo "b";
                        $event->cancel();
                        if (PlayerBase::isFreeze($player)) {
                            PlayerBase::removeFreeze($player);
                            AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM . TE::BOLD . TE::LIGHT_PURPLE . $player->getName() . TE::RESET . TE::GRAY . " unfrozen by " . TE::BOLD . TE::YELLOW . $damager->getName());
                        } else {
                            PlayerBase::addFreeze($player);
                            AdvancedPractice::getInstance()->getServer()->broadcastMessage(AdvancedPractice::SYSTEM . TE::BOLD . TE::LIGHT_PURPLE . $player->getName() . TE::RESET . TE::GRAY . " frozen by " . TE::BOLD . TE::YELLOW . $damager->getName());
                        }
                    }
                    if ($item->getId() === ItemIds::BOOK) {
                        $event->cancel();
                        $damager->sendMessage(TE::LIGHT_PURPLE . $player->getName() . TE::GRAY . " playing from" . TE::WHITE . ": " . TE::LIGHT_PURPLE . Country::getCountry($player));
                        $damager->sendMessage(TE::LIGHT_PURPLE . $player->getName() . TE::GRAY . " playing on device" . TE::WHITE . ": " . TE::LIGHT_PURPLE . PlayerBase::getDevice($player));
                    }
                }
            }
        }
    }

    /**
     * @param PlayerMoveEvent $event
     * @return void
     */
    public function onPlayerMoveEvent(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        if (PlayerBase::isFreeze($player)) {
            $player->getNetworkSession()->onTitle(TE::LIGHT_PURPLE . "YOU HAVE BEEN FROZEN");
            $event->cancel();
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreakEvent(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $direction = $player->getDirectionVector()->multiply(4);
        if (PlayerBase::isStaff($player)) {
            if ($player->getInventory()->getItemInHand()->getId() === ItemIds::COMPASS) {
                $player->teleport(Position::fromObject($event->getPlayer()->getPosition()->add($direction->getX(), $direction->getY(), $direction->getZ()), $event->getPlayer()->getWorld()));
                $event->cancel();
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onBlockPlaceEvent(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (PlayerBase::isStaff($player)) {
            if ($player->getInventory()->getItemInHand()->getId() === ItemIds::PACKED_ICE) {
                $event->cancel();
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onPlayerChatEvent(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        if (Data::isPermanentlyMuted($player->getName())) {
            $player->sendMessage(AdvancedPractice::SYSTEM . TE::LIGHT_PURPLE . "You were permanently muted from chat!");
            $event->cancel();
        } elseif (Data::isTemporarilyMuted($player->getName())) {
            $file = Data::getData("players_timemuteds");
            $result = $file->get($player->getName());

            if ($result["time_mute"] > time()) {
                $player->sendMessage(AdvancedPractice::SYSTEM . TE::LIGHT_PURPLE . "You were temporarily muted, time remaining" . TE::WHITE . ": " . TE::LIGHT_PURPLE . Time::getTimeLeft($result["time_mute"]));
                $event->cancel();
            } else {
                Data::removeMute($player->getName(), false);
            }
        }
        if (AdvancedPractice::getInstance()->globalChat && !PlayerBase::isStaff($player)) {
            $player->sendMessage(AdvancedPractice::SYSTEM . TE::LIGHT_PURPLE . "Global chat is currently disabled!");
            $event->cancel();
            return;
        }
        if (!isset($this->spam[$player->getName()])) {
            $this->spam[$player->getName()] = time();
        }
        if ((time() - $this->spam[$player->getName()]) < 5) {
            if ($player->hasPermission("expire.chat.command")) return;

            $time = time() - $this->spam[$player->getName()];
            $player->sendMessage(AdvancedPractice::SYSTEM . TE::LIGHT_PURPLE . "You have to wait " . Time::getTimeElapsed(3 - $time) . " to write in the chat again!");
            $event->cancel();
        } else {
            $this->spam[$player->getName()] = time();
        }
    }

    /**
     * @param PlayerPreLoginEvent $event
     */
    public function onPlayerPreLoginEvent(PlayerPreLoginEvent $event): void
    {
        $player = $event->getPlayerInfo();
        if (Data::isPermanentlyBanned($player->getUsername())) {
            $file = Data::getData("players_banneds");
            $result = $file->get($player->getUsername());

            if ($event->getIp() === $result["address"] || $player->getUuid()->toString() === $result["uuid"]) {
                $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, TE::BOLD . TE::LIGHT_PURPLE . "You were banned from the network permanently" . TE::RESET . "\n" . TE::GRAY . "You were banned by: " . TE::AQUA . $result["sender_name"] . TE::RESET . "\n" . TE::GRAY . "Reason: " . TE::AQUA . $result["reason_of_ban"] . TE::RESET . "\n" . TE::BLUE . TE::BOLD . "Discord: " . TE::RESET . TE::AQUA . "https://discord.gg/4aEFcayfch");
            }
        } elseif (Data::isTemporarilyBanned($player->getUsername())) {
            $file = Data::getData("players_timebanneds");
            $result = $file->get($player->getUsername());

            if ($result["time_ban"] > time()) {
                if ($event->getIp() === $result["address"] || $player->getUuid()->toString() === $result["uuid"]) {
                    $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, TE::BOLD . TE::LIGHT_PURPLE . "You were banned from the network temporarily" . TE::RESET . "\n" . TE::GRAY . "You were banned by: " . TE::AQUA . $result["sender_name"] . TE::RESET . "\n" . TE::GRAY . "Reason: " . TE::AQUA . $result["reason_of_ban"] . TE::RESET . "\n" . TE::GRAY . "Time left: " . TE::GREEN . Time::getTimeLeft($result["time_ban"]) . TE::RESET . "\n" . TE::BLUE . TE::BOLD . "Discord: " . TE::RESET . TE::AQUA . "https://discord.gg/4aEFcayfch");
                }
            } else {
                Data::removeBan($player->getUsername(), false);
            }
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     * @throws \ReflectionException
     */
    public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();
        $pl = $event->getOrigin();
        if ($packet instanceof LoginPacket) {
            foreach (AdvancedPractice::getInstance()->getServer()->getNetwork()->getInterfaces() as $interface) {
                if ($interface instanceof RakLibInterface) {
                    try {
                        $reflector = new ReflectionProperty($interface, "interface");
                        $reflector->setAccessible(true);
                        // $reflector->getValue($interface)->sendOption("packetLimit", 900000000000);
                    } catch (Exception $exception) {
                        AdvancedPractice::getInstance()->getLogger()->error($exception->getMessage());
                    }
                }
            }
            /*
            if (isset($packet->clientData["Waterdog_IP"])) {
                $class = new ReflectionClass($pl);
                $property = $class->getProperty("ip");
                $property->setAccessible(true);
                $property->setValue($pl, $packet->clientData["Waterdog_IP"]);
            }
            PlayerBase::addDevice($packet);
            */
        }
    }

}