<?php namespace Jacob\Orix\leaderboard;

use pocketmine\player\Player;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\entity\UpdatableFloatingText;
use function str_repeat;

class Leaderboard {

    public array $leaderboards = [];

    private array $spawn_positions = [
        "kills" => "31:77:-14",
        "deaths" => "45:77:0",
        "wins" => "31:77:14",
        "losses" => "400:59:570",
        "welcome" => "7:82:0",
    ];

    public function init() : void {
        foreach ($this->spawn_positions as $name => $pos) {
            $e = explode(":", $pos);
            $txt = new UpdatableFloatingText($this->getLeaderboardType($name), new Position((float)((int)$e[0])+0.5, (int)$e[1], (float)((int)$e[2])+0.5, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()), 0, $name);
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $txt->update($this->getLeaderboardType($name), $player);
            }
            $this->leaderboards[] = $txt;
        }
    }

    public function updateForPlayer(Player $player) : void {
        foreach ($this->leaderboards as $le) {
            if ($le instanceof UpdatableFloatingText) {

                $le->update($this->getLeaderboardType($le->getType()), $player);
            }
        }
    }

    public function updateAll() : void
    {
        foreach ($this->leaderboards as $le) {
            if ($le instanceof UpdatableFloatingText) {
                foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    $le->remove($player);
                    $le->update($this->getLeaderboardType($le->getType()), $player);
                }
            }
        }
    }


    public function updateLeaderboards() : void {
        foreach ($this->leaderboards as $le) {
            if ($le instanceof UpdatableFloatingText) {
                foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    $le->update($this->getLeaderboardType($le->getType()), $player);
                }
            }
        }
    }




    public function getLeaderboardType(string $type) : string
    {
        $ret = "";
        if ($type == "kills") {
            $array = [];
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $array[$player->getName()] = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getKills();
            }
            $all = AdvancedPractice::getInstance()->playerDatabase->getAll();
            foreach ($all as $name => $data) {
                if (isset($array[$name])) continue;
                $array[$name] = $data["kills"];
            }
            arsort($array);
            $ret .= str_repeat("§r §b", 5) . "§l§dKills Leaderboard" . str_repeat("§f §d", 5) . "\n§r";
            $pos = 0;
            foreach ($array as $name => $kills) {
                if ($pos > 9) break;
                if ($pos < 3) {
                    $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $kills . "\n";
                } else $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $kills . "\n";
                $pos++;
            }
        }
        if ($type == "deaths") {
            $array = [];
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $array[$player->getName()] = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDeaths();
            }
            $all = AdvancedPractice::getInstance()->playerDatabase->getAll();
            foreach ($all as $name => $data) {
                if (isset($array[$name])) continue;
                $array[$name] = $data["deaths"];
            }
            arsort($array);
            $ret .= str_repeat("§r §b", 5) . "§l§dDeaths Leaderboard" . str_repeat("§f §d", 5) . "\n§r";
            $pos = 0;
            foreach ($array as $name => $deaths) {
                if ($pos > 9) break;
                if ($pos < 3) {
                    $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $deaths . "\n";
                } else $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $deaths . "\n";
                $pos++;
            }
        }
        if ($type == "wins") {
            $array = [];
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $array[$player->getName()] = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getWins();
            }
            $all = AdvancedPractice::getInstance()->playerDatabase->getAll();
            foreach ($all as $name => $data) {
                if (isset($array[$name])) continue;
                $array[$name] = $data["wins"];
            }
            arsort($array);
            $ret .= str_repeat("§r §b", 5) . "§l§dWins Leaderboard" . str_repeat("§f §d", 5) . "\n§r";
            $pos = 0;
            foreach ($array as $name => $ks) {
                if ($pos > 9) break;
                if ($pos < 3) {
                    $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $ks . "\n";
                } else $ret .= "§5" . ($pos + 1) . ". §r§7" . $name . "§r§7 | §5" . $ks . "\n";
                $pos++;
            }
        }
        if ($type == "losses") {
            $array = [];
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $array[$player->getName()] = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getLosses();
            }
            $all = AdvancedPractice::getInstance()->playerDatabase->getAll();
            foreach ($all as $name => $data) {
                if (isset($array[$name])) continue;
                $array[$name] = $data["losses"];
            }
            arsort($array);
            $ret .= str_repeat("§r §b", 5) . "§l§dTOP DUEL LOSSES" . str_repeat("§f §d", 5) . "\n§r";
            $pos = 0;
            foreach ($array as $name => $ksd) {
                if ($pos > 9) break;
                if ($pos < 3) {
                    $ret .= "§5[" . ($pos + 1) . "] §r§7" . $name . " §5" . $ksd . "\n";
                } else $ret .= "§5[" . ($pos + 1) . "] §r§7" . $name . " §5" . $ksd . "\n";
                $pos++;
            }
        }
        if ($type == "welcome") {
            $array = [];
            foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $array[$player->getName()] = AdvancedPractice::getSessionManager()->getPlayerSession($player)->getWins();
            }
            $all = AdvancedPractice::getInstance()->playerDatabase->getAll();
            foreach ($all as $name => $data) {
                if (isset($array[$name])) continue;
                $array[$name] = $data["wins"];
            }
            arsort($array);
            $ret .= str_repeat("§r §b", 0) . "§r§f§4§lMisty §r§7Network | §r§fSeason I" . str_repeat("§f §d", 0) . "\n§r";
            $pos = 0;
            foreach ($array as $name => $ks) {
                if ($pos > 2) break;
                if ($pos < 2) {
                    $ret .= " ";
                } else $ret .= "\n" ."§r§aStarted on January 7th" . "\n" ."§r§cEnd on February 7th" . "\n" . " " . "\n" ."§d§lCommon Commands" . "\n" ."§r§e/report <player> <reason>" . "\n" ."§r§e/duel <player>" . "\n" ."§a§l* NEW * §r§e/duel" . "\n" ."  " . "\n" ."§o§7discord.gg/misty";
                $pos++;
            }
        }
        return $ret;
    }
}