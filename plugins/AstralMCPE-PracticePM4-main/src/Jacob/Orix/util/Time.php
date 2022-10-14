<?php

namespace Jacob\Orix\util;

use Jacob\Orix\AdvancedPractice;

use pocketmine\utils\TextFormat as TE;
use pocketmine\math\Vector3;

class Time {

    const VALID_FORMATS = ["minutes", "hours", "seconds", "days"];

    /**
     * @param String $timeFormat
     */
    public static function intToString(String $timeFormat){
        $format = str_split($timeFormat);
        $time = null; 
        for($i = 0; $i < count($format); $i++){
            switch($format[$i]){
                case "m":
                $time = "minutes";
                break;
                case "h":
                $time = "hours";
                break;
                case "d":
                $time = "days";
                break;
                case "s":
                $time = "seconds";
                break;
            }
        }
        return $time;
    }

    /**
     * @param String $timeFormat
     * @return Int
     */
    public static function stringToInt(String $timeFormat) : Int {
        $format = str_split($timeFormat);
        $characters = "";
        for($i = 0; $i < count($format); $i++){
            if(is_numeric($format[$i])){
            	$characters .= $format[$i];
            	continue;
            }
        }
        return $characters;
    }

    /**
     * @param Int $time
     * @param String $timeFormat
     * @return Int
     */
    public static function getFormatTime(Int $time, String $timeFormat) : Int {
        $value = null;
        switch(self::intToString($timeFormat)){
            case "minutes":
            $value = time() + ($time * 60);
            break;
            case "hours":
            $value = time() + ($time * 3600);
            break;
            case "days":
            $value = time() + ($time * 86400);
            break;
            case "seconds":
            $value = time() + ($time * 1);
            break;
        }
        return $value;
    }
    
    /**
	 * @param mixed $time
	 * @return String
	 */
	public static function getTimeElapsed($time) : String {
		$seconds = $time % 60;
		$minutes = null;
		$hours = null;
		$days = null;

		if($time >= 60){
			$minutes = floor(($time % 3600) / 60);
			if($time >= 3600){
				$hours = floor(($time % (3600 * 24)) / 3600);
				if($time >= 3600 * 24){
					$days = floor($time / (3600 * 24));
				}
			}
		}
		return ($minutes !== null ? ($hours !== null ? ($days !== null ? "$days days " : "")."$hours hours " : "")."$minutes minutes " : "")."$seconds seconds";
	}

	/**
	 * @param mixed $time
	 * @return String
	 */
	public static function getTimeLeft($time) : String {
		$remaning = $time - time();

		$s = $remaning % 60;
		$m = null;
		$h = null;
		$days = null;

		if($remaning >= 60){
			$m = floor(($remaning % 3600) / 60);
			if($remaning >= 3600){
				$h = floor(($remaning % 86400) / 3600);
				if($remaning >= 3600 * 24){
					$days = floor($remaning / 86400);
				}
			}
		}
		return ($m !== null ? ($h !== null ? ($days !== null ? "$days days " : "")."$h hours " : "")."$m minutes " : "")."$s seconds";
	}
}

?>