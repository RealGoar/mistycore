<?php namespace Jacob\Orix\duel;

use FilesystemIterator;
use Jacob\Orix\generator\BaseRaiding;
use Jacob\Orix\util\Utilities;
use pocketmine\command\defaults\GamemodeCommand;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Jacob\Orix\generator\SumoDuels;
use pocketmine\world\WorldCreationOptions;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\generator\OneVsOneDuel;
use function mt_rand;
use function rmdir;
use function str_shuffle;
use pocketmine\world\generator\Flat;
use function strtolower;
use function unlink;

class Duel {

    private Player $player1;
    private Player $player2;

    private bool $ranked = false;
    private string $type = "";

    private string $worldName = "";

    public function __construct(bool $ranked, string $type, Player $one, Player $two) {
        AdvancedPractice::getInstance()->matches += 1;
        $one->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Found duel! Loading...");
        $two->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "Found duel! Loading...");
        AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
        AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
        AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelTimer();
        AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelTimer();
        $this->worldName = str_shuffle("123456789abcdefghijklmnopqrstuvwxyz");
        $options = new WorldCreationOptions();
        // Baseraiding
        if($type === "BaseRaiding"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "baseraiding-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            // If the player selected the trapper role
           /* if(AdvancedPractice::getSessionManager()->getPlayerSession($one)->isTrapper() === true && AdvancedPractice::getSessionManager()->getPlayerSession($one)->isBaseRaider() === false){
                $one->sendMessage("");
            } else {
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->setTrapper(true);
            }

            // If The player selected the trapper role
            if(AdvancedPractice::getSessionManager()->getPlayerSession($two)->isTrapper() === true && AdvancedPractice::getSessionManager()->getPlayerSession($two)->isBaseRaider() === false){
                $two->sendMessage("");
            } else {
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->setTrapper(true);
            }

            // If the player selected the base raider role
            if(AdvancedPractice::getSessionManager()->getPlayerSession($one)->isBaseRaider() === true && AdvancedPractice::getSessionManager()->getPlayerSession($one)->isTrapper() === false){
                $one->sendMessage("");
            } else {
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->setBaseRaider(true);
            }

            // If the player selected the base raider role
            if(AdvancedPractice::getSessionManager()->getPlayerSession($two)->isBaseRaider() === true && AdvancedPractice::getSessionManager()->getPlayerSession($two)->isTrapper() === false){
                $two->sendMessage("");
            } else {
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->setBaseRaider(true);
            }*/
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setTrapper(true);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setBaseRaider(true);
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Gapple"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "gapple-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "BuildUHC"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "builduhc-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Dragon"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "dragon-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "NoDebuff"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
           $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
           Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "nodebuff-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
       }
        if($type === "Ability"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "ability-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Fist"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
           AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "fist-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Knockback"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
           $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
           mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "knockback-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "SafeRoom"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
        $this->ranked = $ranked;
        $this->type = $type;
        $this->player1 = $one;
        AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
        AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
        mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "saferoom-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
    } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Soup"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
            $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "soup-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
        } else {
           $options->setGeneratorClass(OneVsOneDuel::class);
        }
        if($type === "Combo"){
            // TODO: FIX THE BASE RAIDER GENERATOR CLASS!!
            $this->ranked = $ranked;
            $this->type = $type;
           $this->player1 = $one;
            $this->player2 = $two;
            AdvancedPractice::getSessionManager()->getPlayerSession($one)->setDuelClass($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($two)->setDuelClass($this);
            mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
            Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "combo-duelmap", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
       } else {
            $options->setGeneratorClass(OneVsOneDuel::class);
        }
              // World Loader
        $options->setSeed(0);
        $options->setSpawnPosition(new Vector3(0,100,0));
        AdvancedPractice::getInstance()->getServer()->getWorldManager()->generateWorld($this->worldName, $options);
        $this->ranked = $ranked;
        $this->type = $type;
        $this->player1 = $one;
        $this->player2 = $two;
        AdvancedPractice::getInstance()->getServer()->getWorldManager()->loadWorld($this->worldName);
        $world = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName($this->worldName);
        // Initializer
        AdvancedPractice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($one, $two, $world, $type) : void {
            if($this->type === "BaseRaiding") {
                $one->teleport(new Location(-1800, 66, -1839, $world, 0, 0)); // trapper
                $two->teleport(new Location(-1800, 66, -1800, $world, 0, 0)); // baseraider
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            } else {
                $one->teleport(new Location(24, 110, 40, $world, 0, 0));
                $two->teleport(new Location(24, 110, 10, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "NoDebuff") {
                $one->teleport(new Location(-200, 65, -284, $world, 0, 0));
                $two->teleport(new Location(-200, 65, -200, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "BuildUHC") {
                $one->teleport(new Location(183, 100, 350, $world, 0, 0));
                $two->teleport(new Location(183, 100, 290, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "SafeRoom") {
                $one->teleport(new Location(246, 67, 245, $world, 0, 0));
                $two->teleport(new Location(246, 67, 245, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Dragon") {
                $one->teleport(new Location(194, 88, 365, $world, 0, 0));
                $two->teleport(new Location(191, 88, 308, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Gapple") {
                $one->teleport(new Location(57076, 98, 2124, $world, 0, 0));
                $two->teleport(new Location(57076, 98, 2050, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Ability") {
                $one->teleport(new Location(400, 65, 473, $world, 0, 0));
                $two->teleport(new Location(400, 65, 400, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Combo") {
                $one->teleport(new Location(1397, 61, -32, $world, 0, 0));
                $two->teleport(new Location(1397, 61, 21, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Soup") {
                $one->teleport(new Location(18087, 92, 11077, $world, 0, 0));
                $two->teleport(new Location(18087, 92, 11149, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Knockback") {
                $one->teleport(new Location(363, 74, 208, $world, 0, 0));
                $two->teleport(new Location(323, 74, 230, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
            if($this->type === "Fist") {
                $one->teleport(new Location(191, 88, 303, $world, 0, 0));
                $two->teleport(new Location(191, 88, 363, $world, 0, 0));
                $one->sendTitle("§bDuel Start!");
                $two->sendTitle("§bDuel Start!");
                $one->sendSubTitle("§r§7§oFight to the death!");
                $two->sendSubTitle("§r§7§oFight to the death!");
                AdvancedPractice::getSessionManager()->getPlayerSession($one)->giveStringedKit(strtolower($type));
                AdvancedPractice::getSessionManager()->getPlayerSession($two)->giveStringedKit(strtolower($type));
            }
        }), 20);
    }

    /**
     * @return array<Player>
     */
    public function getPlayers() : array {
        return [$this->player1, $this->player2];
    }

    public function getType() : string {
        return $this->type;
    }

    public function isRanked() : bool {
        return $this->ranked;
    }

    public function winDuel(Player $winner, Player $looser) : void {
        AdvancedPractice::getInstance()->getServer()->broadcastMessage("§l§7[§d!§7] - §r§7" . $winner->getName()." has beat ".$looser->getName()." in a ".($this->ranked ? "Ranked " : "Un-Ranked")." ".$this->type." duel!");
        $spawn = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn();
        if(!$winner->isOnline()) {
            $winner->teleport($spawn);
        }
        if(!$looser->isOnline()) {
            $looser->teleport($spawn);
        }
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->setDuelClass(null);
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->setDuelClass(null);
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->setBaseRaider(false);
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->setTrapper(false);
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->setTrapper(false);
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->setBaseRaider(false);
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->giveHubKit();
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->giveHubKit();
        $winner->setHealth($winner->getMaxHealth());
        $winner->getHungerManager()->setFood($winner->getHungerManager()->getMaxFood());
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->addWins(1);
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->addLosses(1);
        $looser->setHealth($winner->getMaxHealth());
        $looser->getHungerManager()->setFood($winner->getHungerManager()->getMaxFood());
        AdvancedPractice::getSessionManager()->getPlayerSession($winner)->removeDuelTimer();
        AdvancedPractice::getSessionManager()->getPlayerSession($looser)->removeDuelTimer();
        $winner->sendTitle("§a§lVICTORY", "§r§fYou won the match");
        $looser->sendTitle("§l§cDEFEAT", "§r§fYou lost the match");
        if ($this->isRanked()) {
            $amt = mt_rand(12, 16);
            $winner->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have gained " . $amt . " ELO!");
            $looser->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You have lost ".$amt." ELO!");
            AdvancedPractice::getSessionManager()->getPlayerSession($winner)->addElo($amt, $this->getType());
            AdvancedPractice::getSessionManager()->getPlayerSession($looser)->addElo(-$amt, $this->getType());
        }
        AdvancedPractice::getInstance()->matches -= 1;
        AdvancedPractice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() : void {
            AdvancedPractice::getInstance()->getServer()->getWorldManager()->unloadWorld(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName($this->worldName));
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = AdvancedPractice::getInstance()->getServer()->getDataPath() . "/worlds/" . $this->worldName, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            /** @var SplFileInfo $fileInfo */
            foreach ($files as $fileInfo) {
                if ($filePath = $fileInfo->getRealPath()) {
                    if ($fileInfo->isFile()) {
                        unlink($filePath);
                    } else {
                        rmdir($filePath);
                    }
                }
            }
            rmdir($worldPath);
        }), 60);
    }
}