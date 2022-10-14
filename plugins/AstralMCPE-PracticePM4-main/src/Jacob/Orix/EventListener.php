<?php namespace Jacob\Orix;

use Davewats\PvPCore\PvPCore;
use diduhless\parties\Parties;
use diduhless\parties\session\Session;
use http\Cookie;
use Jacob\Orix\duel\Duel;
use Jacob\Orix\entity\PearlEntity;
use Jacob\Orix\form\NewModesQueueForm;
use Jacob\Orix\form\parties\CreateParty;
use Jacob\Orix\form\parties\EventParty;
use Jacob\Orix\form\parties\PartyMenu;
use Jacob\Orix\parties\Party;
use Jacob\Orix\task\BlockBreakTask;
use pocketmine\block\Block;
use pocketmine\block\Concrete;
use pocketmine\block\Glass;
use pocketmine\block\Planks;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Anvil;
use pocketmine\block\Chest;
use pocketmine\block\EnchantingTable;
use pocketmine\block\EnderChest;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\FenceGate;
use pocketmine\block\TrappedChest;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\lang\Language;
use pocketmine\math\Vector3;
use pocketmine\permission\BanEntry;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\EntityFactory;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\Server;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\particle\SplashParticle;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\GoldenApple;
use pocketmine\item\Item;
use pocketmine\item\MushroomStew;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV2;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\world\sound\EntityAttackSound;
use Jacob\Orix\form\cosmetics\CosmeticsForm;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use Jacob\Orix\form\NormalForm;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\EnderPearl;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use Jacob\Orix\constants\ParticleConstants;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeParticle;
use Jacob\Orix\form\CosmeticCategoriesForm;
use Jacob\Orix\form\FFAForm;
use Jacob\Orix\form\ProfileForm;
use Jacob\Orix\form\QueueForm;
use Jacob\Orix\form\cosmetics\ParticleSelectionForm;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use Jacob\Orix\form\cosmetics\WingSelectionForm;
use Jacob\Orix\form\cosmetics\TrailSelectionForm;
use skymin\bossbar\BossBarAPI;
use Tournament\KaOz\Loader;
use function in_array;
use function is_null;
use function mt_rand;
use function str_replace;

class EventListener implements Listener
{
    /** @var Loader $plugin */
    private $plugin;

    public function __construct(AdvancedPractice $plugin) {
        $this->plugin = $plugin;
    }
    private array $interactCooldown = [];

    private array $chatCooldown = [];

    public function onLogin(PlayerLoginEvent $event): void
    {
        AdvancedPractice::getSessionManager()->createSession($event->getPlayer());
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        if (AdvancedPractice::getSessionManager()->getPlayerSession($event->getPlayer())->isFrozen()) {
            $event->cancel();
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if ($player !== null && $player->isOnline()) {
            switch ($packet->pid()) {
                case AnimatePacket::NETWORK_ID:
                    switch ($packet->action) {
                        case AnimatePacket::ACTION_SWING_ARM:
                            $player->getServer()->broadcastPackets($player->getViewers(), [$packet]);
                            $event->cancel();
                            break;
                    }
                    break;
            }
        }
    }


    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
        $event->setJoinMessage("§2 + §a" . $player->getName());
        $player->sendTitle("§l§dMisty Network Practice");
        $player->sendSubTitle("§dSeason 1");
        $player->setGamemode(GameMode::SURVIVAL());
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        $player->teleport(new Location(3, 82, 0, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld(), 0, 0));
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->open();
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->updateScoreTag();
        AdvancedPractice::getLeaderboard()->updateForPlayer($player);
        //$bossbar = new BossBarAPI();
        //$bossbar->sendBossBar($player, "§l§dConnect at misty.lol!", 0, "0", 0);
    }

    public function onTeleport(EntityTeleportEvent $event): void
    {
        $where = $event->getTo();
        if ($event->getFrom()->getWorld()->getFolderName() == $where->getWorld()->getFolderName()) return;
        if ($where->getWorld()->getFolderName() == AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
            $en = $event->getEntity();
            if ($en instanceof Player) {
                AdvancedPractice::getLeaderboard()->updateForPlayer($en);
            }
        }
    }
    /*public function onTapToPot(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $id = $item->getId();
        if($item instanceof \pocketmine\item\SplashPotion){
            $item = $event->getItem();
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $player->setHealth($player->getHealth() + 8);
            $color = new Color(255, 0, 0); // Red color
            $particle = new PotionSplashParticle($color);
            $world = $player->getPosition()->getWorld();
            $world->addParticle(new Position($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $world), $particle);
        }
    }*/
   /* public function onTapToPearl(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if($item instanceof EnderPearl){
            if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getSuffocatingCooldown() > 0){
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot pearl out of here because you are suffocating.");
                $event->cancel();
                return;
            }
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0) {
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a pearl cooldown! §7(" . AdvancedPractice::getUtils()->secondsToEnderpearlCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPearlCooldown();
            $item = $event->getItem();
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $entity = new PearlEntity(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld()), null, $player);
            $entity->spawnToAll();
        }
    }*/
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $event->setQuitMessage("§4 - §c" . $player->getName());
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isInParty()){
            if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPartyRole() === Party::LEADER){
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->getParty()->disbandPartyByQuit();
            }else{
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->getParty()->removeMemberByQuit($player);
            }
        }

        AdvancedPractice::getSessionManager()->closeSession($player);
    }
    public function onBlockPlace(BlockPlaceEvent $event){
        $p = $event->getPlayer();
            if($event->getBlock() instanceof Concrete){
                AdvancedPractice::getInstance()->getScheduler()->scheduleRepeatingTask(new BlockBreakTask($event->getBlock()), 20);
                return;
            }
    }
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();

        if ($player instanceof Player) {
            $cause = $event->getCause();
            if($cause === EntityDamageEvent::CAUSE_VOID) {
                if ($player->getWorld() === AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")) {
                    $this->getPlugin()->getTournment()->quit($player, true);
                    if (count($this->getPlugin()->getTournment()->getQueuePlayers()) > 0) {
                        $this->getPlugin()->quitUI($player);
                    }
                }
                if ($this->getPlugin()->getTournment()->isSpectator($player)) {
                    $this->getPlugin()->getTournment()->closePlayer($player);
                }
            }
            if ($cause == EntityDamageEvent::CAUSE_VOID) {
                if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()){
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()->winDuel(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelOpponent(), $player);
                } else {
                    $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                    $event->cancel();
                }

                $player->teleport($player->getWorld()->getSafeSpawn());
            } else if ($cause == EntityDamageEvent::CAUSE_FALL) {
                $event->cancel();
            }
        }
            }
        public function onTimerEnd(PlayerMoveEvent $event)
        {
            $player = $event->getPlayer();
            if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()){
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer() < 1) {
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelOpponent()->sendMessage("§l§7[§d!§7] - §r§7Your duel has ended because your timer ran out!");
                AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()->winDuel(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelOpponent(), $player);
                $player->sendMessage("§l§7[§d!§7] - §r§7Your duel has ended because your timer ran out!");
            }
                }
        }
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $hit = $event->getEntity();
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        $cause = $event->getCause();

        if ($entity instanceof Player && $damager instanceof Player) {
            $event->setAttackCooldown(10);
        }
        if(AdvancedPractice::getSessionManager()->getPlayerSession($entity)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->isSpectator() === true){
            $event->cancel();
            return;
        }
        //if(AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getCps() > 20){
          //  $damager->kick("                      §5§lANTI-CHEAT\n§r§dOur anti-cheat has detected high cps from you.\n          §r§dIf this detection happens again, \n          §r§dyou will be banned for cheating.");
            //AdvancedPractice::getSessionManager()->getPlayerSession($damager)->addWarns(1);

        if ($hit instanceof Player and $damager instanceof Player) {
            if ($hit->getWorld()->getFolderName() == AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()) {
                $event->cancel();
                return;
            }
            if ($damager->getInventory()->getItemInHand()->getCustomName() === "§bFreeze §7(Right-Click a Player)") {
                $event->cancel();
                $session = AdvancedPractice::getSessionManager()->getPlayerSession($hit);
                $session->setFrozen(true);
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have been frozen!");
                $hit->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have frozen " . $hit->getName() . "!");
                if ($session->isFrozen()) {
                    $session->setFrozen(false);
                    $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have been un-frozen!");
                    $hit->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have un-frozen " . $hit->getName() . "!");

                }
            }
            if ($event->getModifier(EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) < 0.0) $event->cancel();
            if ($hit->getHealth() - $event->getFinalDamage() < 0.1) {
                $pos = $hit->getPosition();
                $shouldDoDeathMessage = true;
                if (AdvancedPractice::getSessionManager()->getPlayerSession($hit)->getDuelClass() !== null) {
                    AdvancedPractice::getSessionManager()->getPlayerSession($hit)->getDuelClass()->winDuel(AdvancedPractice::getSessionManager()->getPlayerSession($hit)->getDuelOpponent(), $hit);
                    $shouldDoDeathMessage = false;
                }
                $event->cancel();
                if (AdvancedPractice::getSessionManager()->getPlayerSession($damager)->getSettings()["rekit"]) {
                    $world = strtolower($damager->getWorld()->getFolderName());
                    AdvancedPractice::getSessionManager()->getPlayerSession($damager)->giveStringedKit($world);
                }
                $damager->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Successfully automatically re-kitted.");
                AdvancedPractice::getSessionManager()->getPlayerSession($hit)->giveHubKit();
                $hit->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                AdvancedPractice::getSessionManager()->getPlayerSession($hit)->justDied();
                AdvancedPractice::getSessionManager()->getPlayerSession($damager)->justKilled();
                $hit->getWorld()->addParticle($hit->getPosition(), new HugeExplodeParticle());
                $hit->setHealth(20);
                $damager->setHealth(20);
                AdvancedPractice::getSessionManager()->getPlayerSession($hit)->setSuffocating(false);
                AdvancedPractice::getSessionManager()->getPlayerSession($damager)->setSuffocating(false);

                if ($shouldDoDeathMessage) {
                    AdvancedPractice::getInstance()->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . $hit->getName() . " has been killed by " . $damager->getName());
                }
                return;
            }
            foreach ([$hit, $damager] as $player) {
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime() < 1) {
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->setCombatTagged();
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have been combat-tagged.");
                } else {
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->setCombatTagged();
                }
            }
            if (AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("combo")) {
                $event->setKnockBack(0.400);


            } else {
                $event->setKnockBack(0.388);
            }
        }
}
    public function throwPearl(PlayerItemUseEvent $event) : void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $thrown = false;
        if ($item->getId() == ItemIds::ENDER_PEARL) {
            if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getSuffocatingCooldown() > 0){
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot pearl out of here because you are suffocating.");
                $event->cancel();
                return;
            }
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0) {
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a pearl cooldown! §7(" . AdvancedPractice::getUtils()->secondsToEnderpearlCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setPearlCooldown();
            if ($thrown) {
                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
            }
        }
    }

    public
    function onRespawn(PlayerRespawnEvent $event): void
    {
        $player = $event->getPlayer();
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
        $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
    }

    public
    function onDrop(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if ($player->getGamemode()->getEnglishName() == "Creative") return;
        $event->cancel();
    }

    public function onItemUse(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $hand = $player->getInventory()->getItemInHand();
        if ($item instanceof MushroomStew) {
            $item = $event->getItem();
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $player->setHealth($player->getHealth() + 6);
        }
    }
    public
    function rightclick(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        if (!isset($this->interactCooldown[$player->getName()])) $this->interactCooldown[$player->getName()] = time();
        else if (time() - $this->interactCooldown[$player->getName()] < 1) {
            return;
        }
        $this->interactCooldown[$player->getName()] = time();
        $name = $event->getItem()->getCustomName();
        $hub_items = ["§r§dCosmetics §r§7(Right-Click)", "§r§dUn-Ranked Duels §r§7(Right-Click)", "§r§dRanked Duels §r§7(Right-Click)", "§r§dLeave Queue §r§7(Right-Click)", "§r§dFree For All §r§7(Right-Click)", "§r§dProfile §r§7(Right-Click)", "§r§dParty §r§7(Right-Click)", "§r§dHCF Modes §r§7(Right-Click)", "§r§dStart party events §r§7(Right-Click)", "§r§dFight other party §r§7(Right-Click)", "§r§dView party members §r§7(Right-Click)", "§r§cDisband party §r§7(Right-Click)", "§r§cLeave party §r§7(Right-Click)"];
        if (in_array($name, $hub_items)) {
            switch ($name) {
                case "§r§dHCF Modes §r§7(Right-Click)":
                    $player->sendForm(new NewModesQueueForm());
                    break;
                    //PARTIES
                case "§r§dParty §r§7(Right-Click)":
                    //$player->sendMessage("§l§7[§d!§7] - §r§7Coming Soon");
                    $player->sendForm(new PartyMenu());
                    break;
                case '§r§dStart party events §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){
                        $player->sendForm(new EventParty);
                    }else{
                        $player->sendMessage('Just the leader can start an event.');
                    }
                    break;
                case '§r§dFight other party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    $player->sendMessage('L');
                    if($playerParty->isLeader($player->getName())){

                    }else{
                        $player->sendMessage('Just the leader can start an event.');
                    }
                    break;
                case '§r§dView party members §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    $player->sendMessage("\n");
                    $player->sendMessage($playerParty->getLeader()."'s party members:");

                    foreach ($playerParty->getMembers() as $memberName){
                        if($memberName === $player->getName()){
                            $player->sendMessage('- '.$memberName. TextFormat::AQUA.' (You)');
                        }else{
                            $player->sendMessage('- '.$memberName);
                        }
                    }
                    break;
                case '§r§cDisband party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){
                        $playerParty->disbandParty();
                    }else{
                        $player->sendMessage('You cant disband the party, because you are not the leader.');
                    }
                    break;
                case '§r§cLeave party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){
                        $playerParty->disbandParty();
                    }else{
                        $player->sendMessage('Quitting...');
                        $playerParty->removeMember($player);


                        $player->sendMessage('You left the party.');
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                    }
                    break;
                case "§r§dUn-Ranked Duels §r§7(Right-Click)":
                    $player->sendForm(new QueueForm());
                    break;
                case "§r§dRanked Duels §r§7(Right-Click)":
                    $player->sendForm(new QueueForm(true));
                    break;
                case "§r§dLeave Queue §r§7(Right-Click)":
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have left the queue.");
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->endQueue();
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                    $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                    break;
                case "§r§dFree For All §r§7(Right-Click)":
                    $player->sendForm(new FFAForm());
                    break;
                case "§r§dProfile §r§7(Right-Click)":
                    $player->sendForm(new ProfileForm());
                    break;
                case "§r§dCosmetics §r§7(Right-Click)":
                    $form = new CosmeticsForm();
                    $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                    $callback = function (Player $player, ?string $data) use ($form, $session): void {
                        if ($data === null) {
                            return;
                        }
                        switch ($data) {
                            case "Particles":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("particle." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setParticle($data !== "Disable" ? ParticleConstants::SPIRAL_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your particle to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your particle."));
                                    }
                                };
                                $cosmeticForm = new ParticleSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::SPIRAL_PARTICLES as $SPIRAL_PARTICLE => $id) {
                                    $button = ($player->hasPermission("particle." . strtolower(str_replace(" ", "_", $SPIRAL_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($SPIRAL_PARTICLE));
                                    $cosmeticForm->addButton($session->getParticle() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($SPIRAL_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back", NormalForm::IMAGE_TYPE_PATH);
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Trails":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("trail." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setTrail($data !== "Disable" ? ParticleConstants::TRAIL_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your trail to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your trail."));
                                    }
                                };
                                $cosmeticForm = new TrailSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::TRAIL_PARTICLES as $TRAIL_PARTICLE => $id) {
                                    $button = ($player->hasPermission("trail." . strtolower(str_replace(" ", "_", $TRAIL_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($TRAIL_PARTICLE));
                                    $cosmeticForm->addButton($session->getTrail() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($TRAIL_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back");
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Wing Particles":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("wing." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setWing($data !== "Disable" ? ParticleConstants::WING_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your wing to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your wing."));
                                    }
                                };
                                $cosmeticForm = new WingSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::WING_PARTICLES as $WING_PARTICLE => $id) {
                                    $button = ($player->hasPermission("wing." . strtolower(str_replace(" ", "_", $WING_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($WING_PARTICLE));
                                    $cosmeticForm->addButton($session->getWing() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($WING_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back");
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Capes":
                                $player->sendForm(AdvancedPractice::getInstance()->mainCapesForm());
                                break;
                        }
                    };
                    $form->setCallback($callback);
                    $form->addButton("Particles");
                    $form->addButton("Trails");
                    $form->addButton("Wing Particles");
                    $form->addButton("Capes");
                    $form->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Exit");
                    $player->sendForm($form);
            }
        }
    }
    public
    function interact(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (!isset($this->interactCooldown[$player->getName()])) $this->interactCooldown[$player->getName()] = time();
        else if (time() - $this->interactCooldown[$player->getName()] < 1) {
            return;
        }
        $this->interactCooldown[$player->getName()] = time();
        $name = $event->getItem()->getCustomName();
        $hub_items = ["§r§dCosmetics §r§7(Right-Click)", "§r§dUn-Ranked Duels §r§7(Right-Click)", "§r§dRanked Duels §r§7(Right-Click)", "§r§dLeave Queue §r§7(Right-Click)", "§r§dFree For All §r§7(Right-Click)", "§r§dProfile §r§7(Right-Click)", "§r§dParty §r§7(Right-Click)", "§r§dHCF Modes §r§7(Right-Click)", "§r§dStart party events §r§7(Right-Click)", "§r§dFight other party §r§7(Right-Click)", "§r§dView party members §r§7(Right-Click)", "§r§cDisband party §r§7(Right-Click)", "§r§cLeave party §r§7(Right-Click)"];
        if (in_array($name, $hub_items)) {
            switch ($name) {
                case "§r§dHCF Modes §r§7(Right-Click)":
                    $player->sendForm(new NewModesQueueForm());
                    break;
                case "§r§dParty §r§7(Right-Click)":
                    //$player->sendMessage("§l§7[§d!§7] - §r§7Coming Soon");
                    $player->sendForm(new PartyMenu());
                    break;
                case '§r§dStart party events §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){

                    }else{
                        $player->sendMessage('Just the leader can start an event.');
                    }
                    break;
                case '§r§dFight other party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    $player->sendMessage('L');
                    if($playerParty->isLeader($player->getName())){

                    }else{
                        $player->sendMessage('Just the leader can start an event.');
                    }
                    break;
                case '§r§dView party members §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    $player->sendMessage("\n");
                    $player->sendMessage($playerParty->getLeader()."'s party members:");

                    foreach ($playerParty->getMembers() as $memberName){
                        if($memberName === $player->getName()){
                            $player->sendMessage('- '.$memberName. TextFormat::AQUA.' (You)');
                        }else{
                            $player->sendMessage('- '.$memberName);
                        }
                    }
                    break;
                case '§r§cDisband party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){
                        $playerParty->disbandParty();
                    }else{
                        $player->sendMessage('You cant disband the party, because you are not the leader.');
                    }
                    break;
                case '§r§cLeave party §r§7(Right-Click)':
                    $playerParty = AdvancedPractice::getPartyManager()->getParty($player->getName());
                    if($playerParty->isLeader($player->getName())){
                        $playerParty->disbandParty();
                    }else{
                        $player->sendMessage('Quitting...');
                        $playerParty->removeMember($player);

                        $player->sendMessage('You left the party.');
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                    }
                    break;
                case "§r§dUn-Ranked Duels §r§7(Right-Click)":
                    $player->sendForm(new QueueForm());
                    break;
                case "§r§dRanked Duels §r§7(Right-Click)":
                    $player->sendForm(new QueueForm(true));
                    break;
                case "§r§dLeave Queue §r§7(Right-Click)":
                    $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have left the queue.");
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->endQueue();
                    AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                    $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                    break;
                case "§r§dFree For All §r§7(Right-Click)":
                    $player->sendForm(new FFAForm());
                    break;
                case "§r§dProfile §r§7(Right-Click)":
                    $player->sendForm(new ProfileForm());
                    break;
                case "§r§dCosmetics §r§7(Right-Click)":
                    $form = new CosmeticsForm();
                    $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                    $callback = function (Player $player, ?string $data) use ($form, $session): void {
                        if ($data === null) {
                            return;
                        }
                        switch ($data) {
                            case "Particles":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("particle." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setParticle($data !== "Disable" ? ParticleConstants::SPIRAL_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your particle to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your particle."));
                                    }
                                };
                                $cosmeticForm = new ParticleSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::SPIRAL_PARTICLES as $SPIRAL_PARTICLE => $id) {
                                    $button = ($player->hasPermission("particle." . strtolower(str_replace(" ", "_", $SPIRAL_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($SPIRAL_PARTICLE));
                                    $cosmeticForm->addButton($session->getParticle() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($SPIRAL_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back", NormalForm::IMAGE_TYPE_PATH);
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Trails":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("trail." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setTrail($data !== "Disable" ? ParticleConstants::TRAIL_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your trail to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your trail."));
                                    }
                                };
                                $cosmeticForm = new TrailSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::TRAIL_PARTICLES as $TRAIL_PARTICLE => $id) {
                                    $button = ($player->hasPermission("trail." . strtolower(str_replace(" ", "_", $TRAIL_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($TRAIL_PARTICLE));
                                    $cosmeticForm->addButton($session->getTrail() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($TRAIL_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back");
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Wing Particles":
                                $callback = function (Player $player, ?string $data): void {
                                    if ($data) {
                                        if ($data !== "Disable" && !$player->hasPermission("wing." . strtolower(str_replace(" ", "_", $data)))) {
                                            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have not unlocked this cosmetic yet.");
                                            return;
                                        }
                                        $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);
                                        $session->setWing($data !== "Disable" ? ParticleConstants::WING_PARTICLES[strtoupper($data)] : null);
                                        $player->sendMessage($data !== "Disable" ? (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully set your wing to $data.") : (TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have successfully disabled your wing."));
                                    }
                                };
                                $cosmeticForm = new WingSelectionForm();
                                $cosmeticForm->setPrevious($form);
                                foreach (ParticleConstants::WING_PARTICLES as $WING_PARTICLE => $id) {
                                    $button = ($player->hasPermission("wing." . strtolower(str_replace(" ", "_", $WING_PARTICLE))) ? TextFormat::YELLOW : TextFormat::LIGHT_PURPLE) . ucwords(strtolower($WING_PARTICLE));
                                    $cosmeticForm->addButton($session->getWing() !== $id ? $button : TextFormat::GREEN . ucwords(strtolower($WING_PARTICLE)));
                                }
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Disable");
                                $cosmeticForm->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Back");
                                $cosmeticForm->setCallback($callback);
                                $player->sendForm($cosmeticForm);
                                break;
                            case "Capes":
                                $player->sendForm(AdvancedPractice::getInstance()->mainCapesForm());
                                break;
                        }
                    };
                    $form->setCallback($callback);
                    $form->addButton("Particles");
                    $form->addButton("Trails");
                    $form->addButton("Wing Particles");
                    $form->addButton("Capes");
                    $form->addButton(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Exit");
                    $player->sendForm($form);
            }
        }
    }

    public function onBuild(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $player = $event->getPlayer();
        $e = VanillaBlocks::QUARTZ();
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if($event->getBlock() instanceof Concrete) {
            return;
        }
        if ($player->getGamemode()->getEnglishName() == "Creative") return;
        if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone() > 0) {
            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot break, place, or open anything! §7(" . AdvancedPractice::getUtils()->secondsToBoneCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone()) . ")");
            $event->cancel();
            return;
        } else {
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->isTrapper()) {
                return;
            }
            if ($player->getGamemode()->getEnglishName() == "Creative") return;
            $event->cancel();
        }
    }
    public static $blocks = [];


    public function handlePlace(BlockPlaceEvent $event): void
    {
        $block = $event->getBlock();

        self::$blocks[$block->getPosition()->getFloorX() . ':' . $block->getPosition()->getFloorY() . ':' . $block->getPosition()->getFloorZ()] = time() + 10;
    }

    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if($event->getBlock() instanceof Concrete){
            return;
        }

        if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone() > 0) {
            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot break, place, or open anything! §7(" . AdvancedPractice::getUtils()->secondsToBoneCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone()) . ")");
            $event->cancel();
            return;
        } else {

            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->isTrapper()) {
                if ($event->getBlock() instanceof Glass) {
                    $event->cancel();
                }
                if ($event->getBlock() instanceof Planks) {
                    $event->cancel();
                } else {
                    return;
                }
            }
            if ($player->getGamemode()->getEnglishName() == "Creative") return;
            $event->cancel();
        }
    }

    public function onHunger(PlayerExhaustEvent $event): void
    {
        $event->cancel();
    }
    public function invopen(InventoryOpenEvent $event){
        $player = $event->getPlayer();
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
    }
    public function onConsume(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $player->getInventory()->getItemInHand()->pop(1);
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if($item instanceof Cookie){
            $event->cancel();
        }
        if ($item instanceof GoldenApple) {
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0) {
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are still on a gapple cooldown! §7(" . AdvancedPractice::getUtils()->secondsToGappleCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown()) . ")");
                $event->cancel();
                return;
            }
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->setGappleCooldown();
            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You are now on a gapple cooldown.");
        }
    }


    public function onCommandPreProcess(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        if ($msg[0] == "/") {
            if (!is_null(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass())) {
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "This is a non-duel command.");
                $event->cancel();
                return;
            }
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime() > 0) {
                if (AdvancedPractice::getInstance()->getServer()->isOp($player->getName())) return;
                $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot use commands while in combat.");
                $event->cancel();
            }
        }
    }
    public function Enderpearl(ProjectileLaunchEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof \pocketmine\entity\projectile\EnderPearl){
            $entity->kill();
        }
    }
    /*public function potionHitEvent(ProjectileHitEvent $event): void
    {
        $type = $event->getEntity();
        $owner = $type->getOwningEntity();
        if ($owner instanceof Player) {
            if ($type instanceof SplashPotion) {
                $owner->setHealth($owner->getHealth() + 8);
                foreach ($type->getWorld()->getNearbyEntities($type->getBoundingBox()->expand(2, 5, 2)) as $entity) {
                    if ($entity instanceof Player) {
                        if ($entity->getName() == $owner->getName()) continue;
                        $entity->setHealth($owner->getHealth() + 8);
                    }
                }
            }
        }
    }*/

    public function onRegain(EntityRegainHealthEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->updateScoreTag();
        }
    }

    public function onMove2(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        //if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCps() > 20){
          //  $player->kick("                      §5§lANTI-CHEAT\n§r§dOur anti-cheat has detected high cps from you.\n          §r§dIf this detection happens again, \n          §r§dyou will be banned for cheating.");
            //AdvancedPractice::getSessionManager()->getPlayerSession($player)->addWarns(1);
        
        
        if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getSettings()["sprint"]) $player->setSprinting(true);
   //     if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass()){
   //         AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
   //         $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
   //     }
    }

    public function onPacket(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
         if ($packet instanceof LevelSoundEventPacket) {
             AdvancedPractice::getSessionManager()->getPlayerSession($player)->addCps();
              if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getSettings()["cps"]) {
                   $pk = new TextPacket();
                   $pk->type = TextPacket::TYPE_JUKEBOX_POPUP;
                  $pk->message = "§dCPS: " . AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCps();
                    $player->getNetworkSession()->sendDataPacket($pk);
                 }
                  if ($packet instanceof InventoryTransactionPacket) {
                     if ($packet->trData instanceof UseItemOnEntityTransactionData) {
                         AdvancedPractice::getSessionManager()->getPlayerSession($player)->addCps();
                     }
                     if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getSettings()["cps"]) {
                         $pk = new TextPacket();
                         $pk->type = TextPacket::TYPE_JUKEBOX_POPUP;
                         $pk->message = "§dCPS: " . AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCps();
                          $player->getNetworkSession()->sendDataPacket($pk);
                      }
                   }
                 }
             }
        public
        function onItemUse34(EntityDamageEvent $event)
        {
            $player = $event->getEntity();

        }

        public
        function onInteract3(PlayerItemUseEvent $event): void
        {
            $player = $event->getPlayer();
            $name = $player->getName();
            $item = $event->getItem()->getCustomName();
            $iteme = $event->getPlayer()->getInventory()->getItemInHand();
                switch ($item) {
                    case "§7[§b§lTRAPPER§r§7] (Right-Click)":
                        $player->sendMessage("§l§7[§d!§7] - §r§7You have selected the trapper role.");
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->setTrapper(true);
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->createQueue("baseraiding", true);
                        break;
                    case "§7[§d§lRAIDER§r§7] (Right-Click)":
                        $player->sendMessage("§l§7[§d!§7] - §r§7You have selected the base-raider role.");
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->setBaseRaider(true);
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->createQueue("baseraiding", true);
                        break;
                    case TextFormat::GREEN . "Spectator Toggle Off":
                        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
                        $player->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                        $player->setGamemode(GameMode::SURVIVAL());
                        break;
                    case "§aTeleport to a Random Player §7(Right-Click)":
                        $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Teleporting you to a random player.");
                        $onlinePlayers = [];
                        foreach ($player->getServer()->getOnlinePlayers() as $onlinePlayer) {
                            $onlinePlayers[] = $onlinePlayer;
                        }
                        $number = count($onlinePlayers);
                        $rand = $onlinePlayers[mt_rand(0, $number - 1)];
                        $playerName = $rand->getName();
                        $randx = $rand->getPosition()->getX();
                        $randy = $rand->getPosition()->getY();
                        $randz = $rand->getPosition()->getZ();
                        $randworld = $rand->getWorld();
                        $player->teleport(new Location($randx, $randy, $randz, $randworld, 0, 0));
                        break;
                    case "§5Flight §7(Right-Click)":
                        $player->setFlying(true);
                        $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Flight has been enabled.");
                        $e = VanillaItems::FEATHER()->setCustomName("§5Flight §7(Right-Click) | §aENABLED");
                        $player->getInventory()->setItem(2, $e);
                        break;
                    case "§5Flight §7(Right-Click) | §aENABLED":
                        $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Flight has been disabled.");
                        $e = VanillaItems::FEATHER()->setCustomName("§5Flight §7(Right-Click)");
                        $player->getInventory()->setItem(2, $e);
                        $player->setFlying(false);
                        break;
                    case "§dVanish §7(Right-Click)":
                        $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Vanish has been enabled.");
                        $e = VanillaItems::BONE()->setCustomName("§dVanish §7(Right-Click) | §aENABLED");
                        $player->getInventory()->setItem(4, $e);
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 20000, 255, false));
                        break;
                    case "§dVanish §7(Right-Click) | §aENABLED":
                        $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Vanish has been disabled.");
                        $e = VanillaItems::BONE()->setCustomName("§dVanish §7(Right-Click)");
                        $player->getInventory()->setItem(4, $e);
                        $player->getEffects()->remove(VanillaEffects::INVISIBILITY());
                        break;
                }
            }
    public function onQuit12389(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if($this->getPlugin()->getTournment()->inQueue($player) or $this->getPlugin()->getTournment()->inGame($player)) {
            $this->getPlugin()->getTournment()->quit($player);
        }
        if($this->getPlugin()->getTournment()->isSpectator($player)) {
            $this->getPlugin()->getTournment()->closePlayer($player);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($this->getPlugin()->getTournment()->isRunning() and ($this->getPlugin()->getTournment()->inQueue($player) or $this->getPlugin()->getTournment()->inGame($player))) {
            $this->getPlugin()->getTournment()->quit($player, true);
            if(count($this->getPlugin()->getTournment()->getQueuePlayers()) > 0) {
                $this->getPlugin()->quitUI($player);
            }
            $event->setKeepInventory(true);
        }
        if($this->getPlugin()->getTournment()->isSpectator($player)) {
            $this->getPlugin()->getTournment()->closePlayer($player);
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage2132(EntityDamageEvent $event){
        $entity = $event->getEntity();
        $cause = $event->getCause();
        if($entity instanceof Player) {
            if($this->getPlugin()->getTournment()->isRunning() and $this->getPlugin()->getTournment()->inQueue($entity)) {
                $event->cancel();
            }
        }
        if($cause === EntityDamageEvent::CAUSE_VOID) {
            if ($this->getPlugin()->getTournment()->isRunning() and $entity->getWorld() === AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")) {
                $this->getPlugin()->getTournment()->quit($entity, true);
                if (count($this->getPlugin()->getTournment()->getQueuePlayers()) > 0) {
                    if ($entity instanceof Player) {
                        $this->getPlugin()->quitUI($entity);
                    }

                }
                if ($this->getPlugin()->getTournment()->isSpectator($entity)) {
                    $this->getPlugin()->getTournment()->closePlayer($entity);
                }
            }
        }
    }

    public function onDeathV2(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        if ($player->getHealth() - $event->getFinalDamage() < 0.1) {
            if ($this->getPlugin()->getTournment()->isRunning() and ($this->getPlugin()->getTournment()->inQueue($player) or $this->getPlugin()->getTournment()->inGame($player))) {
                $this->getPlugin()->getTournment()->quit($player, true);
                if (count($this->getPlugin()->getTournment()->getQueuePlayers()) > 0) {
                    if($player instanceof Player)
                        $this->getPlugin()->quitUI($player);
                }

            }
            if ($this->getPlugin()->getTournment()->isSpectator($player)) {
                $this->getPlugin()->getTournment()->closePlayer($player);
            }
        }
    }
    public function onEntityDamage21(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();

        if ($player instanceof Player) {
            $cause = $event->getCause();
            if ($cause == EntityDamageEvent::CAUSE_VOID) {
                if ($this->getPlugin()->getTournment()->isRunning() and ($this->getPlugin()->getTournment()->inQueue($player) or $this->getPlugin()->getTournment()->inGame($player))) {
                    $this->getPlugin()->getTournment()->quit($player, true);
                    if (count($this->getPlugin()->getTournment()->getQueuePlayers()) > 0) {
                        if($player instanceof Player)
                            $this->getPlugin()->quitUI($player);
                    }

                }
                if ($this->getPlugin()->getTournment()->isSpectator($player)) {
                    $this->getPlugin()->getTournment()->closePlayer($player);
                }
            }
        }
    }
    
	/**
	 * @priority HIGHEST
	 */
	public function onInteract(PlayerInteractEvent $event): void
	{
		$action = $event->getAction();
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();

		if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $block instanceof FenceGate && $item instanceof ItemPearl) {
			$event->cancel();
			$location = $player->getLocation();
			$direction = $player->getDirectionVector();
			$item->onClickAir($player, $direction);
		}
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isSpectator() === true){
            $event->cancel();
            return;
        }
        if($player->getWorld() === AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("builduhc")){
            return;
        }
        if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone() > 0) {
            $player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You cannot break, place, or open anything! §7(" . AdvancedPractice::getUtils()->secondsToBoneCD(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getBone()) . ")");
            $event->cancel();   
        } else {
            if($player->getEffects()->has(VanillaEffects::NIGHT_VISION())){
                return;
            }
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->isTrapper()) {
                if ($event->getBlock() instanceof Planks) {
                    $event->cancel();
                } else {
                    return;
                }
            }
            if ($player->getGamemode()->getEnglishName() == "Creative") return;
            $event->cancel();
        }
	}

    public function onLevelChange(EntityBlockChangeEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            if($this->getPlugin()->getTournment()->isRunning() and ($this->getPlugin()->getTournment()->inQueue($entity) or $this->getPlugin()->getTournment()->inGame($entity))) {
                $this->getPlugin()->getTournment()->quit($entity);
            }
            if($this->getPlugin()->getTournment()->isSpectator($entity)) {
                $this->getPlugin()->getTournment()->closePlayer($entity);
            }
        }
    }

    /**
     * @return Main
     */
    public function getPlugin(): AdvancedPractice {
        return $this->plugin;
    }
}