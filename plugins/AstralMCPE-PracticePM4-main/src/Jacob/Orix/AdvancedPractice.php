<?php namespace Jacob\Orix;

use AttachableLogger;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use Jacob\Orix\command\CapeCommand;
use Jacob\Orix\command\DisguiseCommand;
use Jacob\Orix\command\PartyCommand;
use Jacob\Orix\command\staff\BanCommand;
use Jacob\Orix\command\staff\BanListCommand;
use Jacob\Orix\command\staff\KickCommand;
use Jacob\Orix\command\staff\MuteCommand;
use Jacob\Orix\command\staff\UnBanCommand;
use Jacob\Orix\command\staff\UnMuteCommand;
use Jacob\Orix\provider\YamlProvider;
use Jacob\Orix\parties\PartyManager;
use Jacob\Orix\command\staff\AlertsCommand;
use Jacob\Orix\command\staff\BanTimeCommand;
use Jacob\Orix\command\staff\CheckDeviceCommand;
use Jacob\Orix\command\staff\ClearCommand;
use Jacob\Orix\command\staff\EnchantCommand;
use Jacob\Orix\command\staff\GamemodeCommand;
use Jacob\Orix\command\staff\GipCommand;
use Jacob\Orix\command\staff\GlobalChatCommand;
use Jacob\Orix\command\staff\HelpCommand;
use Jacob\Orix\command\staff\HistoryCommand;
use Jacob\Orix\command\staff\MsgCommand;
use Jacob\Orix\command\staff\MuteTimeCommand;
use Jacob\Orix\command\staff\ReportCommand;
use Jacob\Orix\command\staff\StaffChatCommand;
use Jacob\Orix\command\staff\StaffCommand;
use Jacob\Orix\command\staff\TellCommand;
use Jacob\Orix\command\staff\WarnCommand;
use Jacob\Orix\command\staff\WCommand;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat as TE;
use Jacob\Orix\command\DuelCommand;
use Jacob\Orix\command\EventCommand;
use Jacob\Orix\command\FreezeCommand;
use Jacob\Orix\command\ItemCommand;
use Jacob\Orix\command\SpectateCommand;
use Jacob\Orix\entity\EnderpearlEntity;
use Jacob\Orix\event\Event;
use Jacob\Orix\task\ServerTimerTask;
use Jacob\Orix\entity\SwitcherBall;
use Jacob\Orix\generator\BaseRaiding;
use Jacob\Orix\generator\SumoDuels;
use Jacob\Orix\task\WorldBlockBreakTask;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\Anvil;
use pocketmine\block\Block;
use pocketmine\block\Chest;
use pocketmine\block\EnchantingTable;
use pocketmine\block\EnderChest;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\FenceGate;
use pocketmine\block\TrappedChest;
use pocketmine\block\VanillaBlocks;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\projectile\Projectile;
use pocketmine\entity\Skin;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\EntityFactory;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use Jacob\Orix\item\ItemListener;
use pocketmine\network\mcpe\protocol\types\GameRule;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\generator\FlatGeneratorOptions;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\InvalidGeneratorOptionsException;
use pocketmine\world\WorldManager;
use Ramsey\Uuid\Rfc4122\Validator;
use Jacob\Orix\command\ReKitCommand;
use Jacob\Orix\command\SpawnCommand;
use Jacob\Orix\cosmetic\Cosmetic;
use Jacob\Orix\duel\Queue;
use Jacob\Orix\generator\OneVsOneDuel;
use Jacob\Orix\leaderboard\Leaderboard;
use Jacob\Orix\task\ParticleDisplayTask;
use Jacob\Orix\particle\WingParticle;
use Jacob\Orix\PlayerPreLoginListener;
use Jacob\Orix\session\SessionManager;
use Jacob\Orix\task\PlayerSecondTask;
use Jacob\Orix\util\Utilities;
use function array_diff;
use function scandir;
use function strlen;
use Jacob\Orix\entity\PearlEntity;
use Jacob\Orix\item\ItemPearl;

class AdvancedPractice extends PluginBase {

	private static self $instance;
	private static SessionManager $sessionManager;
	private static Utilities $utilities;
    private static PartyManager $partyManager;
	private static Leaderboard $leaderboard;
	private static Cosmetic $cosmetic;
    public Event $tournment;
    /** @var Event[] */
    public array $pEvents = [];
	public Config $playerDatabase;

	public array $queues = [];
	public int $matches = 0;
    public array $players = [];

                                        
	public array $modes = [
        "NoDebuff" => [
            "icon" => "textures/items/potion_bottle_splash_heal",
            "kit" => "nodebuff"
        ],
        "Sumo" => [
            "icon" => "textures/items/lead",
            "kit" => "sumo"
        ],
        "Ability" => [
            "icon" => "textures/items/nether_star",
            "kit" => "ability"
        ],
        "Soup" => [
            "icon" => "textures/items/beetroot_soup",
            "kit" => "soup"
        ],
        "Gapple" => [
            "icon" => "textures/items/apple_golden",
            "kit" => "gapple"
        ],
        /*"Fist" => [
            "icon" => "",
            "kit" => "fist"
        ],
        "Combo" => [
            "icon" => "",
            "kit" => "combo"
        ],*/
        /*"BuildUHC" => [
            "icon" => "textures/items/bucket_lava",
            "kit" => "builduhc"
        ],*/
    ];
	public array $newModes = [
        "BaseRaiding" => [
            "icon" => "textures/items/bone",
            "kit" => "baseraiding"
        ],
        /*"SafeRoom" => [
            "icon" => "",
            "kit" => "saferoom"
        ],*/
        "Ability" => [
            "icon" => "textures/items/clock_item",
            "kit" => "ability"
        ],
        /*"Knockback" => [
            "icon" => "",
            "kit" => "knockback"
        ],*/
    ];


	public function onEnable() : void {
		self::$instance = $this;
		self::$sessionManager = new SessionManager();
        self::$partyManager = new PartyManager();
		self::$utilities = new Utilities();
		self::$cosmetic = new Cosmetic();
		self::$cosmetic->init();
        EntityFactory::getInstance()->register(SwitcherBall::class, function(World $world, CompoundTag $nbt) : SwitcherBall {
            return new SwitcherBall(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Switcher"]);
        EntityFactory::getInstance()->register(PearlEntity::class, function (World $world, CompoundTag $tag): PearlEntity {
			return new PearlEntity(EntityDataHelper::parseLocation($tag, $world), null, $tag);
		}, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityLegacyIds::ENDER_PEARL);
		ItemFactory::getInstance()->register(new ItemPearl(), true);

		$this->playerDatabase = new Config($this->getDataFolder()."playerData.yml", Config::YAML);
        self::init();

		YamlProvider::init();
		@mkdir($this->getDataFolder()."capes");
		$this->getServer()->getPluginManager()->registerEvents(new PlayerPreLoginListener(), $this);
		$this->getServer()->getWorldManager()->loadWorld("nodebuff");
		$this->getServer()->getWorldManager()->loadWorld("gapple");
		$this->getServer()->getWorldManager()->loadWorld("fist");
		$this->getServer()->getWorldManager()->loadWorld("combo");
		$this->getServer()->getWorldManager()->loadWorld("builduhc");
		$this->getServer()->getWorldManager()->loadWorld("soup");
		$this->getServer()->getWorldManager()->loadWorld("ability");
		$this->getServer()->getWorldManager()->loadWorld("dragon");
		$this->getServer()->getWorldManager()->loadWorld("sumo");
		$this->getServer()->getWorldManager()->loadWorld("map3");
		$this->getServer()->getWorldManager()->loadWorld("knockback");
		GeneratorManager::getInstance()->addGenerator(OneVsOneDuel::class, "duel", fn() => null);
		GeneratorManager::getInstance()->addGenerator(SumoDuels::class, "duel2", fn() => null);
		GeneratorManager::getInstance()->addGenerator(BaseRaiding::class, "duel3", fn() => null);
		self::$leaderboard = new Leaderboard();
		self::$leaderboard->init();
		$this->getScheduler()->scheduleRepeatingTask(new ServerTimerTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new ParticleDisplayTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new PlayerSecondTask(), 20);

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ItemListener(), $this);
		$this->tournment = new Event($this);
		$this->saveDefaultConfig();
		$this->kits = new Config($this->getDataFolder() . "kits.yml", Config::YAML);
		$this->arenas = new Config($this->getDataFolder() . "arenas.yml", Config::YAML);


		$toRegister = [
			new ReKitCommand(),
			new SpawnCommand(),
            new DuelCommand(),
            new ItemCommand(),
            new DisguiseCommand($this),
            new SpectateCommand($this),
            //new CapeCommand(),
            new EventCommand($this),
		];
		$this->getServer()->getCommandMap()->registerAll("AdvancedPractice", $toRegister);
		WingParticle::loadPositions();
        $this->getServer()->getNetwork()->setName("Test");
	}

    const ANTICHEAT = TE::GRAY."[".TE::LIGHT_PURPLE."System".TE::GRAY."]".TE::GRAY." » ".TE::RESET;

    const SYSTEM = TE::GRAY."[".TE::LIGHT_PURPLE."System".TE::GRAY."]".TE::GRAY." » ".TE::RESET;

    /** @var bool */
    public bool $globalChat = false;

    /**
     * @return void
     */
    public static function init() : void {
        // self::registerPermissions();
        $commands = array("kick", "ban", "enchant", "unban", "banlist", "tell", "msg", "w", "gamemode", "kill");
        for($i = 0; $i < count($commands); $i++){
            self::removeCommand($commands[$i]);
        }
        self::registerPermissions();
        self::getInstance()->getServer()->getPluginManager()->registerEvents(new \Jacob\Orix\punishments\EventListener(), self::getInstance());

        self::getInstance()->getServer()->getCommandMap()->register("/warn", new WarnCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/ban", new BanCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/tban", new BanTimeCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/enchant", new EnchantCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/history", new HistoryCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/alerts", new AlertsCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/mod", new StaffCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/sc", new StaffChatCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/unban", new UnBanCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/banlist", new BanListCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/helpop", new HelpCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/clear", new ClearCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/kick", new KickCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/report", new ReportCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/gip", new GipCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/mute", new MuteCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/tmute", new MuteTimeCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/unmute",new UnMuteCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/device", new CheckDeviceCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/gamemode", new GamemodeCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/gchat", new GlobalChatCommand());
        //self::getInstance()->getServer()->getCommandMap()->register("/party", new PartyCommand());

        self::getInstance()->getServer()->getCommandMap()->register("/tell", new TellCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/msg", new MsgCommand());
        self::getInstance()->getServer()->getCommandMap()->register("/w", new WCommand());
    }
    public function mainCapesForm():MenuForm
    {
        foreach($this->getCapesList() as $capes)
        {
            $capeList = [$capes];
            $buttons = [
                new MenuOption($capes)
            ];
        }
        $disableButton = new MenuOption(TextFormat::LIGHT_PURPLE."Disable Cape");
        if(count($this->getCapesList()) == 0)
        {
            $capeList = ["disable"];
            $buttons = [$disableButton];
        }else{
            array_push($capeList, "disable");
            array_push($buttons, $disableButton);
        }

        return new MenuForm
        (
            TextFormat::BOLD.TextFormat::GREEN."Capes",
            "",
            $buttons,

            function(Player $player, int $data)use($capeList):void
            {
                $clicked = $capeList[$data];
                if($clicked == "disable")
                {
                    $this->equipCape($player);
                    return;
                }
                $this->equipCape($player, $clicked);
            }
        );
    }
    public function existsKit(string $kit) {
        $kits = $this->getKits()->getAll();
        if(isset($kits[$kit])) {
            return true;
        }
    }

    public function addKit(Player $player, string $kit) {
        $kits = $this->getKits()->getAll();
        if(isset($kits[$kit])) {
            $player->getInventory()->clearAll();
            foreach($kits[$kit]["Commands"] as $cmd) {
                // $this->getServer()->dispatchCommand(new ConsoleCommandSender, str_replace("{player}", $player->getName(), $cmd));
                $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $player->getLanguage()), $cmd);
            }
            foreach($kits[$kit]["Items"] as $items) {
                $item = explode(":", $items);
                $itemAdd = ItemFactory::getInstance()->get((int)$item[0], (int)$item[1], (int)$item[2]);
                if(isset($item[3])) {
                    $enchantment = StringToEnchantmentParser::getInstance()->parse($item[3]);
                    $itemAdd->addEnchantment(new EnchantmentInstance($enchantment,(int)$item[4]));
                }
                $player->getInventory()->addItem($itemAdd);
            }
            $helmetParsed = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
            $helmetParsed->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 10));
            $chestParsed = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
            $chestParsed->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 10));
            $leggsParsed = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
            $leggsParsed->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 10));
            $bootsParsed=   ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
            $bootsParsed->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 10));
            //  $helmetParsed = StringToItemParser::getInstance()->parse($kits[$kit]["helmet"]);
            //  $chestParsed = StringToItemParser::getInstance()->parse($kits[$kit]["chestplate"]);
            //  $leggsParsed = StringToItemParser::getInstance()->parse($kits[$kit]["leggings"]);
            //  $bootsParsed = StringToItemParser::getInstance()->parse($kits[$kit]["boots"]);
            $player->getArmorInventory()->setHelmet($helmetParsed);
            $player->getArmorInventory()->setChestplate($chestParsed);
            $player->getArmorInventory()->setLeggings($leggsParsed);
            $player->getArmorInventory()->setBoots($bootsParsed);
        }
    }

    public function quitUI(Player $player) {
        $form = new SimpleForm(function(Player $event, int $data = null) {
            if($data !== null) {
                switch($data) {
                    case 0:
                        $this->getTournment()->addSpectator($event);
                        break;
                    case 1:
                        break;
                }
            }
        });
        $form->setTitle("§5-> §l§dTournament §r§5<-");
        $form->setContent("§9Do you want to watch the tournament as a spectator?");
        $form->addButton("Yes");
        $form->addButton("No");
        $form->sendToPlayer($player);
    }

    public function getPrefix(): string {
        return (string)$this->getConfig()->get("prefix");
    }

    public function getStartCountdown(): int {
        return (int)$this->getConfig()->get("start-countdown");
    }

    public function getDuelCountdown(): int {
        return (int)$this->getConfig()->get("duel-countdown");
    }

    public function getKits(): Config {
        return $this->kits;
    }

    public function getArenas(): Config {
        return $this->arenas;
    }

    public function getTournment() {
        return $this->tournment;
    }
    public static function removeCommand(String $command){
        $commandMap = self::getInstance()->getServer()->getCommandMap();
        $cmd = $commandMap->getCommand($command);
        if($cmd === null){
            return;
        }
        $cmd->setLabel("");
        $cmd->unregister($commandMap);
    }

    public function equipCape(Player $player, $cape = null):void
    {
        $skin = $player->getSkin();

        if(!is_null($cape))
        {
            $capeData = $this->createCapeFromPNG($cape);
        }else{
            $capeData = "";
        }

        $setCape = new Skin($skin->getSkinId(), $skin->getSkinData(), $capeData, $skin->getGeometryName(), $skin->getGeometryData());
        $player->setSkin($setCape);
        $player->sendSkin();
    }


    public function createCapeFromPNG(string $cape)
    {
        $file = $this->getDataFolder()."capes/".$cape.".png";
        $img = @imagecreatefrompng($file);
        $data = '';
        $l = (int)@getimagesize($file)[1];
        for ($y = 0; $y < $l; $y++)
        {
            for ($x = 0; $x < 64; $x++)
            {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $data .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $data;
    }

    public function getCapesList():array
    {
        $capes = [];
        foreach(array_diff(scandir($this->getDataFolder()."capes"), ["..", "."]) as $files)
        {
            $data = explode(".", $files);
            if($data[1] == "png")
            {
                array_push($capes, $data[0]);
            }
        }
        return $capes;
    }


    public function getQueuedCountNoEmpty() : int {
		$real = [];
		foreach ($this->queues as $class) {
			if ($class instanceof Queue) {
				if (strlen($class->getMode()) > 2) $real[] = $class;
			}
		}
		return count($real);
	}
    protected function onDisable() : void {
        foreach (EventListener::$blocks as $stringPosition => $time) {
            $pos = explode(':', $stringPosition);
            $world = $this->getServer()->getWorldManager()->getWorldByName("builduhc");
            $position = new Position((int) $pos[0], (int) $pos[1], (int) $pos[2], $world);

            $world->setBlock($position, VanillaBlocks::AIR());

        }
        $this->getScheduler()->scheduleRepeatingTask(new WorldBlockBreakTask(VanillaBlocks::CONCRETE()), 20);
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            $this->playerDatabase->save();
            foreach ($world->getEntities() as $entity) {
                if ($entity instanceof Player) return;
                $entity->flagForDespawn();
            }
        }
    }
    public static function getDefaultConfig($configuration){
        return self::getInstance()->getConfig()->get($configuration);
    }

    /**
     * @param String $commamd
     */

    static private function registerPermissions(): void {
        $permissions = [
            "alerts.command.use", "ban.command.use", "banlist.command.use", "bantime.command.use", "check.command.use",
            "clear.command.use", "enchant.command.use", "gamemode.command.use", "gip.command.use", "history.command.use",
            "kick.command.use", "mute.command.use", "mutetime.command.use", "report.command.use", "sc.command.use",
            "mod.command.use", "unban.command.use", "unmute.command.use", "warn.command.use", "tban.command.use",
            "tmute.command.use"
        ];
        foreach($permissions as $name) {
            PermissionManager::getInstance()->addPermission(new Permission($name));
        }
    }
	public static function getInstance() : self {
		return self::$instance;
	}

	public static function getSessionManager() : SessionManager {
		return self::$sessionManager;
	}

	public static function getUtils() : Utilities {
		return self::$utilities;
	}

	public static function  getLeaderboard() : Leaderboard {
		return self::$leaderboard;
	}

	public static function getCosmetics() : Cosmetic {
		return self::$cosmetic;
	}
        /**
     * @return PartyManager
     */
    public static function getPartyManager(): PartyManager
    {
        return self::$partyManager;
    }

}
