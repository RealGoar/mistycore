<?php namespace Jacob\Orix\session;

use diduhless\parties\session\Session;
use diduhless\parties\session\SessionFactory;
use Jacob\Orix\duel\parties\PartyMatrix;
use Jacob\Orix\parties\Party;
use pocketmine\color\Color;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\VanillaItems;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\TextFormat as C;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\Particle;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\constants\ParticleConstants;
use Jacob\Orix\cosmetic\cosmeticType\WalkingParticleCosmetic;
use Jacob\Orix\duel\Duel;
use pocketmine\utils\TextFormat as TF;
use Jacob\Orix\duel\Queue;
use vale\entity\TestEntity;
use function count;
use function floor;
use function in_array;
use function is_null;
use function str_repeat;
use function strtolower;

class PlayerSession {
    protected ?int $wing;
    protected ?int $trail;
    protected ?int $particle;
	private array $playerData = [];
    protected $disguised=false;
	private array $clicksData = [];
	private array $cooldowns = [
		"chat" => 0,
		"combat" => 0,
		"pearl" => 0,
        "gapple" => 0,
        "pitem" => 0,
        "bone" => 0,
        "duel" => 0,
        "suffocating" => 0,
        "orix" => 0,
        "hero" => 0,
        "party" => 0,
	];
	private array $line = [];
	private $frozen = false;
    private $baseraider = false;
    private $invsender = false;
    private $invite = false;
    private $trapper = false;
    private $spectator = false;
    private $suffocating = false;
    private $queued = false;
    //private $party = false; ???
    //private $partyInvitations; ???
	private string $scoreboardType = "hub";
	private ?WalkingParticleCosmetic $walkingParticle = null;

	private ?Duel $duel = null;
        // Party store.
    private ?string $partyRole;
    private ?Party $party = null;
    private ?array $partyInvitations;

    /** @var PartyMatrix|null */
    private ?PartyMatrix $partyDuel;

	private Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
        $this->particle = null;
        $this->trail = null;
        $this->wing = null;
        $this->partyInvitations = null;
        $this->party = null;
        $this->partyRole = null;

		$this->open();
	}

	public function open() : void {
		$name = $this->player->getName();
		if (!AdvancedPractice::getInstance()->playerDatabase->exists($name)) {
			$this->playerData = [
				"kills" => 0,
				"deaths" => 0,
				"killstreak" => 0,
                "wins" => 0,
                "warns" => 0,
                "losses" => 0,
				"rank" => "None",
				"elo" => [
					"NoDebuff" => 1000,
                    "Gapple" => 1000,
                    "Combo" => 1000,
                    "Sumo" => 1000,
                    "Soup" => 1000,
                    "Ability" => 1000,
                    "Dragon" => 1000,
                    "BaseRaiding" => 1000,
                    "Knockback" => 1000,
                    "Fist" => 1000,
                    "SafeRoom" => 1000,
				],
				"permissions" => [],
				"settings" => [
					"sprint" => false,
					"cps" => true,
                    "rekit" => true,
                    "health" => false,
					"tag" => "",
				]
			];
			return;
		}
		$this->playerData = AdvancedPractice::getInstance()->playerDatabase->get($name);
	}

	public function setSettings(array $settings) : void {
		$this->playerData["settings"] = $settings;
	}

    public function isFrozen(): bool {
        return $this->frozen;
    }

    public function setFrozen(bool $frozen): void {
        $this->frozen = $frozen;
    }
    public function isBaseRaider(): bool {
        return $this->baseraider;
    }
    public function getPing(): ?int {
        return $this->player->getNetworkSession()->getPing();
    }

    public function setBaseRaider(bool $baseraider): void {
        $this->baseraider = $baseraider;
    }
    public function isInvSender(): bool {
        return $this->invsender;
    }

    public function setInviteSender(bool $invsender): void {
        $this->invsender = $invsender;
    }
    public function hasDuelInvite(): bool {
        return $this->invite;
    }
    public function sendDuelInvite(bool $invite, $otherPlayer, $mode, $isRanked): void {
        $this->invite = $invite;
        $this->player->sendMessage("§l§7[§d!§7] - §r§7$otherPlayer requested a duel in $isRanked $mode, type /duel accept to accept.");
    }
    public function isSuffocating(): bool {
        return $this->suffocating;
    }

    public function setSuffocating(bool $suffocating): void {
        $this->suffocating = $suffocating;
    }
    public function isTrapper(): bool {
        return $this->trapper;
    }

    public function setTrapper(bool $trapper): void {
        $this->trapper = $trapper;
    }
    public function isSpectator(): bool {
        return $this->spectator;
    }

    public function setSpectator(bool $spectator): void {
        $this->spectator = $spectator;
    }

	public function getSettings() : array {
		return $this->playerData["settings"];
	}
    public function getParticle(): ?int
    {
        return $this->particle;
    }
    public function setDisguised(bool $value){
        $this->disguised=$value;
    }

    public function isDisguised():bool{
        return $this->disguised!==false;
    }
    public function setParticle(?int $particle): void
    {
        $this->particle = $particle;
    }
    public function getTrail(): ?int
    {
        return $this->trail;
    }

    public function setTrail(?int $trail): void
    {
        $this->trail = $trail;
    }

    public function getWing(): ?int
    {
        return $this->wing;
    }

    public function setWing(?int $wing): void
    {
        $this->wing = $wing;
    }

	public function close() : void {
		if ($this->duel !== null) {
			$this->duel->winDuel($this->getDuelOpponent(), $this->player);
			$this->justDied();
		}
		$this->endQueue();
		$name = $this->player->getName();
		AdvancedPractice::getInstance()->playerDatabase->remove($name);
		AdvancedPractice::getInstance()->playerDatabase->set($name, $this->playerData);
		AdvancedPractice::getInstance()->playerDatabase->save();

       //if($this->partyDuel !== null){
            //TODO: FINISH PARTY DUEL
       //}
	}



	public function getRank() : string {
		return $this->playerData["rank"];
	}
    public function setRank(string $rank) : string {
        return $this->playerData["rank"] = $rank;
    }

	public function justKilled() : void {
		$this->playerData["kills"]++;
		$newKillstreak = ++$this->playerData["killstreak"];
		$this->player->sendMessage("§aYou now have killstreak of ".$newKillstreak."!");
	}

	public function getFullPlayerData() : array {
		return $this->playerData;
	}

	public function justDied() : void {
		$this->playerData["deaths"]++;
		if ($this->playerData["killstreak"] > 0) $this->player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You lost your killstreak of ".$this->playerData["killstreak"]."!");
		$this->playerData["killstreak"] = 0;
	}

	public function getElo(string $type) : int {
		return $this->playerData["elo"][$type];
	}

	public function addElo(int $amount, string $type) : void {
		$this->playerData["elo"][$type] += $amount;
	}
    public function getWins() : int {
        return $this->playerData["wins"];
    }

    public function addWins(int $amount) : void {
        $this->playerData["wins"] += $amount;
    }



    public function getWarns() : int {
        return $this->playerData["warns"];
    }

    public function addWarns(int $amount) : void {
        $this->playerData["warns"] += $amount;
    }
    public function getLosses() : int {
        return $this->playerData["losses"];
    }

    public function addLosses(int $amount) : void {
        $this->playerData["losses"] += $amount;
    }
	public function getKills() : int {
		return $this->playerData["kills"];
	}

	public function getDeaths() : int {
		return $this->playerData["deaths"];
	}

	public function getKDR() : float {
		$pd = $this->playerData;
		$kills = $pd["kills"];
		$deaths = $pd["deaths"];
		return $kills == 0 or $deaths == 0 ? 0.00 : $kills / $deaths;
	}

	public function setPearlCooldown() : void {
		$this->cooldowns["pearl"] = 10;
	}

	public function getPearlCooldown() : int {
		return $this->cooldowns["pearl"];
	}

    public function setSuffocatingCooldown() : void {
        $this->cooldowns["suffocating"] = 2;
    }

    public function getSuffocatingCooldown() : int {
        return $this->cooldowns["suffocating"];
    }
    public function setPartnerItemCooldown() : void {
        $this->cooldowns["pitem"] = 10;
    }

    public function getPartnerItemCooldown() : int {
        return $this->cooldowns["pitem"];
    }
    public function setOrixEventCMDCooldown(): void {
	    $this->cooldowns["orix"] = 10800;
    }
    public function getOrixEventCMDCooldown(): int {
        return $this->cooldowns["orix"];
    }
    public function setHeroEventCMDCooldown(): void {
	    $this->cooldowns["hero"] = 86400;
    }
    public function getHeroEventCMDCooldown(): int {
	    return $this->cooldowns["hero"];
    }
    public function setBone() : void {
        $this->cooldowns["bone"] = 10;
    }

    public function getBone() : int {
        return $this->cooldowns["bone"];
    }
    public function setDuelTimer() : void {
        $this->cooldowns["duel"] = 600;
    }
    public function setPartyDuelTimer(): void{
        $this->cooldowns['party'] = 900;
    }
    public function removeDuelTimer() : void {
	    $this->cooldowns["duel"] = 0;
    }

    public function getDuelTimer() : int {
        return $this->cooldowns["duel"];
    }
    public function setGappleCooldown() : void {
        $this->cooldowns["gapple"] = 10;
    }

    public function getGappleCooldown() : int {
        return $this->cooldowns["gapple"];
    }

	public function setCombatTagged() : void {
		$this->cooldowns["combat"] = 20;
	}

	public function tickCooldowns() : void {
		foreach ($this->cooldowns as $name => $cd) {
			if ($cd < 1) continue;
			$this->cooldowns[$name]--;
		}
	}

	public function getCombatTagTime() : int {
		return $this->cooldowns["combat"];
	}

	public function setDuelClass(?Duel $class) : void {
		$this->duel = $class;
		if (!is_null($class)) $this->scoreboardType = "duel";
	}

	public function getDuelClass() : ?Duel {
		return $this->duel;
	}

    public function setPartyDuel(?PartyMatrix $class): void {
        $this->partyDuel = $class;
        if (!is_null($class)) $this->scoreboardType = $this->getPartyDuel()->getPartyType();
    }

    public function getPartyDuel(): ?PartyMatrix
    {
        return $this->partyDuel;
    }

    /**
     * @return string|null
     */
    public function getPartyRole(): ?string
    {
        return $this->partyRole;
    }

    /**
     * @param string|null $partyRole
     */
    public function setPartyRole(?string $partyRole): void
    {
        $this->partyRole = $partyRole;
    }

    /**
     * @return Party|null
     */
    public function getParty(): ?Party
    {
        return $this->party;
    }

    /**
     * @param Party|null $party
     */
    public function setParty(?Party $party): void
    {
        $this->party = $party;
    }

    /**
     * @return bool
     */
    public function isLeader(){
        if($this->getPartyRole() === Party::LEADER){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isInParty(){
        if($this->getParty() !== null){
            return true;
        }else{
            return false;
        }
    }

	public function addCps() : void {
		array_unshift($this->clicksData, microtime(true));
		if(count($this->clicksData) >= 100) array_pop($this->clicksData);
	}

	public function getCps() : float {
		if(empty($this->clicksData)){
			return 0.0;
		}
		$ct = microtime(true);
		return round(count(array_filter($this->clicksData, static function(float $t) use ($ct) : bool{
				return ($ct - $t) <= 1.0;
			})) / 1.0, 1);
	}

	public function getDuelOpponent() : Player {
		return $this->duel->getPlayers()[0]->getName() == $this->player->getName() ? $this->duel->getPlayers()[1] : $this->duel->getPlayers()[0];
	}
	public function createQueue(string $type, bool $ranked = false) : void {
		$queue = new Queue($type, $ranked, $this->player);
		AdvancedPractice::getInstance()->queues[] = $queue;
		$this->giveQueuedKit();
	}

	public function endQueue() : void {
		$new = [];
		foreach (AdvancedPractice::getInstance()->queues as $class) {
			if ($class instanceof Queue) {
				if ($class->getPlayer()->getXuid() === $this->player->getXuid()) continue;
				$new[] = $class;
			}
		}
		AdvancedPractice::getInstance()->queues = $new;
	}

	public function giveHubKit() : void {
		$player = $this->player;
		$this->scoreboardType = "hub";
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->getEffects()->clear();
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
		$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::IRON_SWORD)->setCustomName("§r§dRanked Duels §r§7(Right-Click)"));
		$player->getInventory()->setItem(1, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD)->setCustomName("§r§dUn-Ranked Duels §r§7(Right-Click)"));
		$player->getInventory()->setItem(2, ItemFactory::getInstance()->get(ItemIds::STONE_SWORD)->setCustomName("§r§dFFA §r§7(Right-Click)"));
        //$player->getInventory()->setItem(4, ItemFactory::getInstance()->get(ItemIds::NAME_TAG)->setCustomName("§r§dHCF Modes §r§7(Right-Click)"));
		$player->getInventory()->setItem(7, ItemFactory::getInstance()->get(ItemIds::NETHER_STAR)->setCustomName("§r§dCosmetics §r§7(Right-Click)"));
		$player->getInventory()->setItem(4, ItemFactory::getInstance()->get(ItemIds::ENDER_EYE)->setCustomName("§r§dHCF Modes §r§7(Right-Click)"));
		$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::ENDER_CHEST)->setCustomName("§r§dProfile §r§7(Right-Click)"));
	}

	public function giveQueuedKit() : void {
		$player = $this->player;
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->getEffects()->clear();
		$player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::DYE)->setCustomName("§r§dLeave Queue §r§7(Right-Click)"));
	}

	public function giveNoDebuffKit() : void {
		$player = $this->player;
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
		$converted = [];
		foreach ($armor as $unconv) {
			$item = ItemFactory::getInstance()->get($unconv);
			$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
			if ($item instanceof Durable) $item->setUnbreakable();
			$converted[] = $item;
		}
		$times = 0;
		foreach ($converted as $conv) {
			if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
			if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
			if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
			if ($times == 3) $player->getArmorInventory()->setBoots($conv);
			$times++;
		}
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));

		$sword->getNamedTag()->setTag("ench", new ListTag());
		if ($sword instanceof Durable) $sword->setUnbreakable();
		$player->getInventory()->addItem($sword);
		$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16));
		$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::STEAK, 0, 64));
		$player->getInventory()->addItem(ItemFactory::getInstance()->get(438, 22, 36));
		$player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
	}
    public function giveKnockbackKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::IRON_HELMET, ItemIds::IRON_CHESTPLATE, ItemIds::IRON_LEGGINGS, ItemIds::IRON_BOOTS];
        $converted = [];
        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::IRON_AXE);
        $sword->getNamedTag()->setTag("ench", new ListTag());
        if ($sword instanceof Durable) $sword->setUnbreakable();
        $player->getInventory()->addItem($sword);
        $stick = ItemFactory::getInstance()->get(ItemIds::STICK);
        $stick->addEnchantment(new EnchantmentInstance(VanillaEnchantments::KNOCKBACK(), 2));
        $player->getInventory()->addItem($stick);
        $snow = ItemFactory::getInstance()->get(ItemIds::EGG, 0, 16);
        $player->getInventory()->addItem($snow);

        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
    }
    public function giveDragonKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $converted = [];
        $helmet = VanillaItems::DRAGON_HEAD()->setCustomName("§r" . TextFormat::DARK_PURPLE .  TextFormat::BOLD . "Dragon Mask")->setCount(1);
        $helmet->setLore(["§l§dLEGENDARY§r \n§l§aEffects§r \n§l§a- §r§7Speed 3§r\n§l§a- §r§7Haste 3§r\n§l§a-§r §7Strength§r\n§l§a-§r §7Resistance§r\n§l§a-§r §7Fire Resistance§r\n§l§a- §r§7Health Boost 3§r\n§l§a-§r§7 Night Vision§r"]);
        $player->getArmorInventory()->setHelmet($helmet);
        $chestplate = VanillaItems::DIAMOND_CHESTPLATE();
        $chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        $chestplate->setUnbreakable(true);
        $player->getArmorInventory()->setChestplate($chestplate);
        $leggings = VanillaItems::DIAMOND_LEGGINGS();
        $leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        $leggings->setUnbreakable(true);
        $player->getArmorInventory()->setLeggings($leggings);
        $boots = VanillaItems::DIAMOND_BOOTS();
        $boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        $boots->setUnbreakable(true);
        $player->getArmorInventory()->setBoots($boots);
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2));

        $sword->getNamedTag()->setTag("ench", new ListTag());
        if ($sword instanceof Durable) $sword->setUnbreakable();
        $player->getInventory()->addItem($sword);
      //  $item = VanillaItems::DRAGON_HEAD()->setCustomName(TextFormat::DARK_PURPLE . "Dragon Mask")->setCount(1);
      //  $item->setLore([
      //      "§r§a§lEffects\n§r§a - §7Speed 3\n§a - §7Haste 3\n§a - §7Strength\n§a - §7Resistance\n§a - §7Fire Resistance\n§a - §7Health Boost 3\n§a - §7Night Vision",
     //   ]);
    //    $player->getInventory()->addItem($item);
        $player->getInventory()->setItem(1, ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 8));
        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
    }

    public function giveAbilityKit() : void {
		$player = $this->player;
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
		$converted = [];
		foreach ($armor as $unconv) {
			$item = ItemFactory::getInstance()->get($unconv);
			$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
			if ($item instanceof Durable) $item->setUnbreakable();
			$converted[] = $item;
		}
		$times = 0;
		foreach ($converted as $conv) {
			if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
			if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
			if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
			if ($times == 3) $player->getArmorInventory()->setBoots($conv);
			$times++;
		}
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));

		$sword->getNamedTag()->setTag("ench", new ListTag());
		if ($sword instanceof Durable) $sword->setUnbreakable();
		$player->getInventory()->addItem($sword);
		$player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16));
		$player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::STEAK, 0, 64));
        
                $item1 = VanillaItems::PUFFERFISH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Combo Ability");
                $item1->setLore([
                "§r§7Right-Click to receive\n§r§7strength two for 8 seconds",
                 ]);
                $player->getInventory()->setItem(7, $item1);
                $item2 = VanillaItems::COOKIE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call");
        $item2->setLore([
            "§r§7Right-Click if under four hearts you are given \n§r§7Resistance 3, Regeneration 5, and Strength 2 for 6 seconds.",
        ]);
                $player->getInventory()->setItem(16, $item2);
                $item3 = VanillaItems::RED_DYE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Beserk Ability");
        $item3->setLore([
            "§r§7Right-Click to receive\n§r§7Strength 2, Resistance 3, and Regeneration 3 for 5 seconds",
        ]);
               $player->getInventory()->setItem(34, $item3);
                $item4 = VanillaItems::SLIMEBALL()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Effect Disabler");

        $item4->setLore([
            "§r§7Hit another player with this slimeball to\n§7clear the effects of the other player.",
        ]);
                $player->getInventory()->setItem(25, $item4);
                $item5 = VanillaItems::BLAZE_POWDER()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Strength II");
        $item5->setLore([
            "§r§7Right-Click to receive\n§7Strength 2 for 4 seconds!",
        ]);
                $player->getInventory()->setItem(26, $item5);
                $item6 = VanillaItems::IRON_INGOT()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Resistance III");
        $item6->setLore([
            "§r§7Right-Click to receive\n§7Resistance 3 for 4 seconds!",
        ]);
                $player->getInventory()->setItem(17, $item6);
                $item7 = VanillaItems::ROTTEN_FLESH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Starving Flesh");
        $item7->setLore([
            "§r§7Hit another player with this rotten flesh to \n§7set the hunger of the player to 1!",
        ]);
                $player->getInventory()->setItem(2, $item7);
                $item8 = VanillaItems::STICK()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Inventory Clogger");
        $item8->setLore([
            "§r§7Hit another player with this stick to \n§7clog the inventory of the player\nwith pickaxes!",
        ]);
                $player->getInventory()->setItem(35, $item8);
		$player->getInventory()->addItem(ItemFactory::getInstance()->get(438, 22, 36));
		$player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
	}
    public function giveSafeRoomKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $converted = [];
        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));

        $sword->getNamedTag()->setTag("ench", new ListTag());
        if ($sword instanceof Durable) $sword->setUnbreakable();
        $player->getInventory()->addItem($sword);
        $player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 8));
        $item2 = VanillaItems::COOKIE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call");
        $item2->setLore([
            "§r§7Right-Click if under four hearts you are given \n§r§7Resistance 3, Regeneration 5, and Strength 2 for 6 seconds.",
        ]);
        $player->getInventory()->setItem(17, $item2);
        $item3 = VanillaItems::RED_DYE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Beserk Ability");
        $item3->setLore([
            "§r§7Right-Click to receive\n§r§7Strength 2, Resistance 3, and Regeneration 3 for 5 seconds",
        ]);
        $player->getInventory()->setItem(35, $item3);
        $item35 = ItemFactory::getInstance()->get(ItemIds::CLOCK);
        $item35->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Life Saver");
        $item35->setLore(["§r§7This item saves your life!"]);
        $player->getInventory()->setItem(16, $item35);
        $item5 = VanillaItems::BLAZE_POWDER()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Strength II");
        $item5->setLore([
            "§r§7Right-Click to receive\n§7Strength 2 for 4 seconds!",
        ]);
        $player->getInventory()->setItem(25, $item5);
        $item6 = VanillaItems::IRON_INGOT()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Resistance III");
        $item6->setLore([
            "§r§7Right-Click to receive\n§7Resistance 3 for 4 seconds!",
        ]);
        $player->getInventory()->setItem(26, $item6);
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(438, 22, 36));
        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
    }
	public function giveComboKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $converted = [];

        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 10));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::PUFFERFISH);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 10));
        $player->getInventory()->addItem($sword);
    }
    public function giveGappleKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $converted = [];

        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->getNamedTag()->setTag("ench", new ListTag());
        if ($sword instanceof Durable) $sword->setUnbreakable();
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));
        $player->getInventory()->addItem($sword);
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 8));
        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
    }
    public function giveBuildUHCKit(): void{
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::IRON_HELMET, ItemIds::IRON_CHESTPLATE, ItemIds::IRON_LEGGINGS, ItemIds::IRON_BOOTS];
        $converted = [];

        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 10));
        $bow = ItemFactory::getInstance()->get(ItemIds::BOW);
        if($bow instanceof Durable)
            $bow->setUnbreakable(true);
        $fish = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE);
        $fish->setCount(16);
        if($fish instanceof Durable)
        $fish->setUnbreakable(true);
        $concrete = ItemFactory::getInstance()->get(ItemIds::CONCRETE);
        $concrete->setCount(64);
        $concrete2 = ItemFactory::getInstance()->get(ItemIds::CONCRETE);
        $concrete2->setCount(64);
        $pickaxe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
        $arrow = ItemFactory::getInstance()->get(ItemIds::ARROW);
        $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 10000, 255, false));
        $arrow->setCount(64);
     //   $rod = ItemFactory::getInstance()->get(ItemIds::FISHING_ROD);
     //   if($rod instanceof Durable){
    //        $rod->setUnbreakable(true);
     //   }
        if($pickaxe instanceof Durable)
            $pickaxe->setUnbreakable(true);
        $player->getInventory()->setItem(0, $sword);
        $player->getInventory()->setItem(1, $bow);
        $player->getInventory()->setItem(2, $fish);
        $player->getInventory()->setItem(3, $concrete);
        $player->getInventory()->setItem(4, $concrete2);
        $player->getInventory()->setItem(5, $pickaxe);
       // $player->getInventory()->setItem(6, $rod);
        $player->getInventory()->setItem(9, $arrow);
    }
    public function giveSoupKit(): void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::IRON_HELMET, ItemIds::IRON_CHESTPLATE, ItemIds::IRON_LEGGINGS, ItemIds::IRON_BOOTS];
        $converted = [];

        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::IRON_SWORD);
        $player->getInventory()->addItem($sword);
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::MUSHROOM_STEW, 0, 35));
    }
        public function giveFistKit() : void{
            $player = $this->player;
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::STEAK, 0, 8));
            $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
        }
    public function giveSumoKit() : void{
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::STEAK, 0, 8));
        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 214748364, 0, false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 214748364, 2, false));
    }
    public const PARTNER_ITEM_NAMES = [
        "switcher" => C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Switcher Ball",
    ];
    public function giveBaseRaidingKit() : void {
        $player = $this->player;
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $armor = [ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $converted = [];
        foreach ($armor as $unconv) {
            $item = ItemFactory::getInstance()->get($unconv);
            $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
            if ($item instanceof Durable) $item->setUnbreakable();
            $converted[] = $item;
        }
        $times = 0;
        foreach ($converted as $conv) {
            if ($times == 0) $player->getArmorInventory()->setHelmet($conv);
            if ($times == 1) $player->getArmorInventory()->setChestplate($conv);
            if ($times == 2) $player->getArmorInventory()->setLeggings($conv);
            if ($times == 3) $player->getArmorInventory()->setBoots($conv);
            $times++;
        }
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));

        $sword->getNamedTag()->setTag("ench", new ListTag());
        if ($sword instanceof Durable) $sword->setUnbreakable();
        $player->getInventory()->addItem($sword);
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16));
        $player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::STEAK, 0, 64));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(438, 22, 36));
        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId(1), 214748364, 0, false));
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isTrapper() === true){
            $player->getInventory()->setItem(27,ItemFactory::getInstance()->get(ItemIds::COBBLESTONE, 0, 64));
            $player->getInventory()->setItem(18,ItemFactory::getInstance()->get(ItemIds::COBBLESTONE, 0, 64));
            $player->getInventory()->setItem(9,ItemFactory::getInstance()->get(ItemIds::STONE_SLAB, 0, 64));
            $item6 = VanillaItems::IRON_INGOT()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Resistance III")->setCount(3);
            $item6->setLore([
                "§r§7Right-Click to receive\n§7Resistance 3 for 4 seconds!",
            ]);
            $player->getInventory()->setItem(17, $item6);
            $item = ItemFactory::getInstance()->get(ItemIds::SNOWBALL);
            $item->setCustomName(self::PARTNER_ITEM_NAMES["switcher"]);
            $item->setLore(["§r§7Hit another player with this snowball to\nswitch positions with the player!"]);
            $item->setCount(3);
            $item->getNamedTag()->setString("switcher", "lol");
            $item3 = ItemFactory::getInstance()->get(ItemIds::CLOCK);
            $item3->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Life Saver");
            $item3->setLore(["§r§7This item saves your life!"]);
            $item3->setCount(2);
            $item6 = VanillaItems::COOKIE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call")->setCount(1);
            $item6->setLore([
                "§r§7Right-Click if under four hearts you are given \n§r§7Resistance 3, Regeneration 5, and Strength 2 for 6 seconds.",
            ]);
            $player->getInventory()->setItem(34, $item6);
            $player->getInventory()->setItem(26, $item3);
            $player->getInventory()->setItem(35, $item);
            $e = VanillaItems::DIAMOND_PICKAXE();
            $e->setUnbreakable(true);
            $e->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
            $player->getInventory()->setItem(7, $e);
        }
        if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isBaseRaider() === true){
            $item1 = VanillaItems::RED_DYE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Beserk Ability")->setCount(3);
            $item1->setLore([
                "§r§7Right-Click to receive\n§r§7Strength 2, Resistance 3, and Regeneration 3 for 5 seconds",
            ]);
            $player->getInventory()->setItem(7, $item1);
            $item2 = VanillaItems::ROTTEN_FLESH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Starving Flesh")->setCount(1);
            $item2->setLore([
                "§r§7Hit another player with this rotten flesh to \n§7set the hunger of the player to 1!",
            ]);
            $player->getInventory()->setItem(34, $item2);
            $item3 = VanillaItems::PUFFERFISH()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Combo Ability")->setCount(3);
            $item3->setLore([
                "§r§7Right-Click to receive\n§r§7strength two for 8 seconds",
            ]);
            $player->getInventory()->setItem(25, $item3);
            $item4 = VanillaItems::STICK()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Inventory Clogger")->setCount(3);
            $item4->setLore([
                "§r§7Hit another player with this stick to \n§7clog the inventory of the player\nwith pickaxes!",
            ]);
            $player->getInventory()->setItem(35, $item4);
            $item5 = VanillaItems::BLAZE_POWDER()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Strength II")->setCount(3);
            $item5->setLore([
                "§r§7Right-Click to receive\n§7Strength 2 for 4 seconds!",
            ]);
            $player->getInventory()->setItem(26, $item5);
            $item6 = VanillaItems::COOKIE()->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Close Call")->setCount(1);
            $item6->setLore([
                "§r§7Right-Click if under four hearts you are given \n§r§7Resistance 3, Regeneration 5, and Strength 2 for 6 seconds.",
            ]);
            $player->getInventory()->setItem(17, $item6);
            $item12 = ItemFactory::getInstance()->get(ItemIds::NETHER_STAR);
            $item12->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Ninja Star");
            $item12->setLore(["§r§7Be sneaky and teleport to the other player!"]);
            $item12->setCount(3);
            $player->getInventory()->setItem(16, $item12);
            $item7 = ItemFactory::getInstance()->get(ItemIds::BONE);
            $item7->setCustomName(C::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Bone");
            $item7->setLore(["§r§7Makes it so the other player cant break, place, or open blocks and items."]);
            $item7->setCount(3);
            $player->getInventory()->setItem(2, $item7);
        }
    }
	public function giveStringedKit(string $kit) : void {
		switch ($kit) {
			case "nodebuff":
				$this->giveNoDebuffKit();
				break;
            case "gapple":
                $this->giveGappleKit();
                break;
            case "fist":
                $this->giveFistKit();
                break;
            case "combo":
                $this->giveComboKit();
                break;
            case "soup":
                $this->giveSoupKit();
                break;
            case "ability":
                $this->giveAbilityKit();
                break;
            case "dragon":
                $this->giveDragonKit();
                break;
            case "sumo":
                $this->giveSumoKit();
                break;
            case "baseraiding":
                $this->giveBaseRaidingKit();
                break;
            case "knockback":
                $this->giveKnockbackKit();
                break;
            case "saferoom":
                $this->giveSafeRoomKit();
                break;
            case "builduhc":
                $this->giveBuildUHCKit();
                break;
		}
	}

	public function getKillstreak() : int {
		return $this->playerData["killstreak"];
	}

    public function updateScoreboard() : void {
        $player = $this->player;
        $this->showScoreboard($player);
        $this->clearLines($player);
        switch($this->scoreboardType) {
            case "hub":
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Online: §f" . count(AdvancedPractice::getInstance()->getServer()->getOnlinePlayers()), $player);
                    $this->addLine("§4 In-Fight: §f" . AdvancedPractice::getInstance()->matches, $player);
                    $this->addLine("§7§r§d§r  ", $player);
                break;
            case "duel":
                $this->addLine("§r  ", $player);
                $this->addLine("§4 Fighting§7: §f". $this->getDuelOpponent()->getName(), $player);
                $this->addLine("§4 Duration§7: §f". AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                $this->addLine("§7§r§d§r ", $player);
                $this->addLine("§a Your Ping§7: §f" . $player->getNetworkSession()->getPing(), $player);
                $this->addLine("§c Their Ping§7: §f". $this->getDuelOpponent()->getNetworkSession()->getPing(), $player);
                $this->addLine("§7§r§d§r  ", $player);
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0) {
                    $this->clearLines($player);
                   $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Fighting§7: §f". $this->getDuelOpponent()->getName(), $player);
                    $this->addLine("§4 Duration§7: §f". AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                    $this->addLine("§4 Gapple§7: §f$pee", $player);
                    $this->addLine("§7§r§d§r ", $player);
                    $this->addLine("§a Your Ping§7: §f" . $player->getNetworkSession()->getPing(), $player);
                    $this->addLine("§c Their Ping§7: §f". $this->getDuelOpponent()->getNetworkSession()->getPing(), $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0){
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Fighting§7: §f". $this->getDuelOpponent()->getName(), $player);
                    $this->addLine("§4 Duration§7: §f". AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                    $this->addLine("§4 Enderpearl§7: §f$pee", $player);
					$this->addLine("§r§d    §f", $player);
                    $this->addLine("§a Your Ping§7: §f" . $player->getNetworkSession()->getPing(), $player);
                    $this->addLine("§c Their Ping§7: §f". $this->getDuelOpponent()->getNetworkSession()->getPing(), $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                break;
            case "ffa":
                $this->addLine("§r  ", $player);
                $this->addLine("§4 Ping: §f". $this->getPing(), $player);
                $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
				$this->addLine("§r§d    §f", $player);
				$this->addLine("§4 Kills: §f".$this->getKills(), $player);
                $this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                $this->addLine("§d§r§d§r  ", $player);
                // Gapple and Combat Cooldown
                if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0 && AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime() > 0){
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown();
                    $pee2 = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Ping: §f". $this->getPing(), $player);
					$this->addLine("§4 Combat: §f$pee2", $player);
					$this->addLine("§r§d    §f", $player);
					$this->addLine("§4 Kills: §f".$this->getKills(), $player);
					$this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                    $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
                    $this->addLine("§4 Gapple: §f$pee", $player);
                    $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§4§r  ", $player);
                }
                // Gapple and Enderpearl Cooldown
                if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0 && AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0){
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown();
                    $pee2 = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Ping: §f". $this->getPing(), $player);
					$this->addLine("§4 Pearl: §f$pee2", $player);
					$this->addLine("§r§d    §f", $player);
                    $this->addLine("§4 Kills: §f".$this->getKills(), $player);
					$this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                    $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
                    $this->addLine("§4 Gapple: §f$pee", $player);
                    $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                // Enderpearl and Combat Cooldown
            if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0 && AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime() > 0)
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0) {
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown();
                    $pee2 = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Ping: §f". $this->getPing(), $player);
					$this->addLine("§4 Combat: §f$pee2", $player);
					$this->addLine("§r§d    §f", $player);
                    $this->addLine("§4 Kills: §f".$this->getKills(), $player);
					$this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                    $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
                    $this->addLine("§4 Pearl: §f$pee", $player);
                    $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime() > 0) {
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getCombatTagTime();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Ping: §f". $this->getPing(), $player);
					$this->addLine("§4 Combat: §f$pee", $player);
					$this->addLine("§r§d    §f", $player);
                    $this->addLine("§4 Kills: §f".$this->getKills(), $player);
					$this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                    $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
                    $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0){
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Ping: §f". $this->getPing(), $player);
					$this->addLine("§4 Enderpearl: §f$pee", $player);
					$this->addLine("§r§d    §f", $player);
                    $this->addLine("§4 Kills: §f".$this->getKills(), $player);
					$this->addLine("§r§4 Deaths: §f".$this->getDeaths(), $player);
                    $this->addLine("§4 Killstreak: §f".$this->getKillstreak(), $player);
                    $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§4§r  ", $player);
                }
                break;
            case 'vsparty':
                $this->addLine("§r  ", $player);
                $this->addLine("§4 Opponent party: §f".$this->getDuelOpponent()->getName().'§7('.')', $player);
                $this->addLine("§d4 Time Left: §f".AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                $this->addLine("§r§d    §f", $player);
                $this->addLine("§7 misty.lol", $player);
                $this->addLine("§7§r§d§r  ", $player);
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown() > 0) {
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getGappleCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§4 Opponent: §f" . $this->getDuelOpponent()->getName(), $player);
                    $this->addLine("§4 Time Left: §f".AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                    $this->addLine("§4 Gapple: §f$pee", $player);
                    $this->addLine("§r§d    §f", $player);
                    $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§d§r  ", $player);
                }
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown() > 0){
                    $this->clearLines($player);
                    $pee = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getPearlCooldown();
                    $this->addLine("§r  ", $player);
                    $this->addLine("§d4 Opponent: §f".$this->getDuelOpponent()->getName(), $player);
                    $this->addLine("§4 Time Left: §f".AdvancedPractice::getUtils()->secondsToDuelTimer(AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelTimer()), $player);
                    $this->addLine("§4 Enderpearl: §f$pee", $player);
                    $this->addLine("§r§d    §f", $player);
                    $this->addLine("§7 misty.lol", $player);
                    $this->addLine("§7§r§d§r  ", $player);

                }
                break;
        }
    }

	public function showScoreboard(Player $player) : void {
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = $player->getName();
		$pk->displayName = "§l§4Misty §r§f§7Network | §fHub";
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 0;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function addLine(string $line, Player $player) : void {
		$score = count($this->line) + 1;
		$this->setLine($score,$line,$player);
	}

	public function removeScoreboard(Player $player) : void {
		$objectiveName = $player->getName();
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $objectiveName;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function clearLines(Player $player) {
		for ($line = 0; $line <= 15; $line++) {
			$this->removeLine($line, $player);
		}
	}

	public function setLine(int $loc, string $msg, Player $player) : void {
		$pk = new ScorePacketEntry();
		$pk->objectiveName = $player->getName();
		$pk->type = $pk::TYPE_FAKE_PLAYER;
		$pk->customName = $msg;
		$pk->score = $loc;
		$pk->scoreboardId = $loc;
		if (isset($this->line[$loc])) {
			unset($this->line[$loc]);
			$pkt = new SetScorePacket();
			$pkt->type = $pkt::TYPE_REMOVE;
			$pkt->entries[] = $pk;
			$player->getNetworkSession()->sendDataPacket($pkt);
		}
		$pkt = new SetScorePacket();
		$pkt->type = $pkt::TYPE_CHANGE;
		$pkt->entries[] = $pk;
		$player->getNetworkSession()->sendDataPacket($pkt);
		$this->line[$loc] = $msg;
	}

	public function removeLine(int $line, Player $player) : void {
		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_REMOVE;
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $player->getName();
		$entry->score = $line;
		$entry->scoreboardId = $line;
		$pk->entries[] = $entry;
		$player->getNetworkSession()->sendDataPacket($pk);
		if (isset($this->line[$line])) {
			unset($this->line[$line]);
		}
	}

	public function getChatColor() : string {
		return $this->playerData["settings"]["chatcolor"];
	}

    public function setInFFA(string $mode) : void {
        $world = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName(strtolower($mode));
        $spawn = $world->getSafeSpawn()->add(0.5, 15, 0.5);
        $session = AdvancedPractice::getSessionManager()->getPlayerSession($this->player);
        $session->setWing(null);
        $session->setParticle(null);
        // If the player is in a party
         $session1 = SessionFactory::getSession($this->player);
             if($session1->getParty() === true) {
             foreach ($session1->getParty()->getMembers() as $member) {
                 $member->getPlayer()->teleport(new Location($spawn->getX(), $spawn->getY(), $spawn->getZ(), $world, 0, 0));
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("nodebuff")){
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                     $this->giveStringedKit("nodebuff");
                 }
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("gapple")){
                     $this->giveStringedKit("gapple");
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                 }
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("soup")){
                     $this->giveStringedKit("soup");
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                 }
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("dragon")){
                     $this->giveStringedKit("dragon");
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                 }
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("sumo")){
                     $this->giveStringedKit("sumo");
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                 }
                 if(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("knockback")){
                     $this->giveStringedKit("nodebuff");
                     AdvancedPractice::getSessionManager()->getPlayerSession($member->getPlayer())->scoreboardType = "ffa";
                 }
                 $member->getPlayer()->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Teleported to FFA.");
                 return;
             }
         }
        $this->player->teleport(new Location($spawn->getX(), $spawn->getY(), $spawn->getZ(), $world, 0, 0));
        $this->giveStringedKit(strtolower($mode));
        $this->player->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Teleported to FFA.");
        $this->scoreboardType = "ffa";
    }

	public function setWalkingParticle(WalkingParticleCosmetic $particle) : void {
		$this->walkingParticle = $particle;
	}

	public function hasWalkingParticle() : bool {
		return !$this->walkingParticle == null;
	}

	public function getWalkingParticle() : Particle {
		return $this->walkingParticle->getParticleType();
	}

	public function updateNameTag() : void {
		$this->player->setNameTag($this->playerData["rank"] == "None" ? "§f".$this->player->getName() : "§d".$this->player->getName());
	}

	public function updateScoreTag() : void {
        if (AdvancedPractice::getSessionManager()->getPlayerSession($this->player)->getSettings()["health"]) {
            $this->player->setScoreTag(floor($this->player->getHealth() / 2) . TF::LIGHT_PURPLE . " ❤");
        }
	}

}