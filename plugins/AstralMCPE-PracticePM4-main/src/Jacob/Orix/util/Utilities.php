<?php namespace Jacob\Orix\util;

use pocketmine\player\Player;
use pocketmine\Server;

class Utilities {

    public function getRankFormatted(string $rank) : string {
        return [
            "None" => "§7[§8Default§r§7]",
            "Aether" => "§7[§9Aether§r§7]",
            "Hero" => "§7[§5Hero§r§7]",
            "Orix" => "§7[§dOrix§r§7]",
            "Media" => "§7[§d§lMedia§r§7]",
            "Trainee" => "§7[§aTrainee§r§7]",
            "Mod" => "§7[§dMod§r§7]",
            "Admin" => "§7[§dAdmin§r§7]",
            "Manager" => "§7[§bManager§r§7]",
            "Owner" => "§7[§4Owner§r§7]"
        ][$rank];
    }

	public function secondsToEnderpearlCD(int $int) : string {
		$m = floor($int / 60);
		$s = floor($int % 60);
		return (($m < 10 ? "0" : "").$m.":".($s < 10 ? "0" : "").$s);
	}
    public function secondsToDuelTimer(int $int) : string {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "").$m.":".($s < 10 ? "0" : "").$s);
    }
    public function secondsToGappleCD(int $int) : string {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "").$m.":".($s < 10 ? "0" : "").$s);
    }
    public function secondsToPartnerItemCD(int $int) : string {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "").$m.":".($s < 10 ? "0" : "").$s);
    }
    public function secondsToBoneCD(int $int) : string {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "").$m.":".($s < 10 ? "0" : "").$s);
    }

    public static function recursiveCopy(string $source, string $target): void
    {
        $dir = opendir($source);
        @mkdir($target);
        while ($file = readdir($dir)) {
            if ($file === "." || $file === "..") {
                continue;
            }
            if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                self::recursiveCopy($source . DIRECTORY_SEPARATOR . $file, $target . DIRECTORY_SEPARATOR . $file);
            } else {
                copy($source . DIRECTORY_SEPARATOR . $file, $target . DIRECTORY_SEPARATOR . $file);
            }
        }
        closedir($dir);
    }

    public static function recursiveDelete(string $path): void
    {;
        if (basename($path) === "." or basename($path) === "..") {
            return;
        }
        foreach (scandir($path) as $item) {
            if ($item === "." or $item === "..") {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                self::recursiveDelete($path . DIRECTORY_SEPARATOR . $item);
            }
            if (is_file($path . DIRECTORY_SEPARATOR . $item)) {
                unlink($path . DIRECTORY_SEPARATOR . $item);
            }
        }
        rmdir($path);
    }
    public static function getFakeNames(){
        $names=["Trapzies","ghxsty","LuckyXTapz","obeseGamerGirl","UnknownXzzz","zAnthonyyy","FannityPE","Vatitelc","StudSport","MCCaffier","Keepuphulk8181","LittleComfy","Decdarle","mythic_d4nger","gambling life","BASIC x VIBES","lawlogic","hutteric","BiggerCobra_1181","Lextech817717","Chnixxor","AloneShun","AddictedToYou","Board","Javail","MusicPqt","REYESOOKIE","Asaurus Rex","Popperrr","oopsimSorry_","lessthan greaterthan","Regrexxx","adam 22","NotCqnadian","brtineyMCPE","samanthaplayzmc","ShaniquaLOL","OptimusPrimeXD","BouttaBust","GamingNut66","NoIdkbruh","ThisIsWhyYoure___","voLT_811","Sekrum","Artificial_","ReadMyBook","urmum__77","idkwhatiatetoday","udkA77161","Stimpy","Adviser","St1pmyPVP","GangGangGg","CoolKid888","AcornChaser78109","anon171717","AnonymousYT","Sintress Balline","Daviecrusha","HeatedBot46","CobraKiller2828","KingPVPYT","TempestG","ThePVPGod","McProGangYT","lmaonocap","NoClipXD","ImHqcking","undercoverbot","reswoownss199q","diego91881","CindyPlayz","HeyItzMe","iTzSkittlesMC","NOHACKJUSTPRO","idkHowToPlay","Bum Bummm","Bigumslol","Skilumsszz","SuperGamer756","ProPVPer2k20","N0S3_P1CK3R84","PhoenixXD","EnderProYT_81919","Ft MePro","NotHaqing","aababah_a","badbtch4life","serumxxx","bigdogoo_","william18187","ZeroLxck","Gamer dan","SuperSAIN","DefNoHax","GoldFox","ClxpKxng","AdamIsPro","XXXPRO655","proshtGGxD","T0PL543","GamerKid9000","SphericalAxeum","ImABot"];
        return $names;
    }
    public static function getPlayerDisplayName($player){
        $result=null;
        if(isset($player) and !is_null($player)){
            if($player instanceof Player){
                $result=$player->getDisplayName();
            }elseif(is_string($player)){
                $p=self::getPlayer($player);
                if(!is_null($p)){
                    $result=self::getPlayerDisplayName($p);
                }
            }
        }
        return $result;
    }
    public static function getPlayer($info){
        $result=null;
        $player=self::getPlayerName($info);
        if($player===null){
            return $result;
        }
        $player=Server::getInstance()->getPlayerExact($player);
        if($player instanceof Player){
            $result=$player;
        }
        return $result;
    }
    public static function getPlayerName($player){
        $result=null;
        if(isset($player) and !is_null($player)){
            if($player instanceof Player){
                $result=$player->getName();
            }elseif(is_string($player)){
                $result=$player;
            }
        }
        return $result;
    }
}