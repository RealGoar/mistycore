<?php

namespace Jacob\Orix\util;

use pocketmine\math\Vector3;

class Translator {
	
	/**
	 * @param Vector3 $position
	 * @return String
	 */
	public static function vector3ToString(Vector3 $position) : String {
		return "{$position->getFloorX()}, {$position->getFloorY()}, {$position->getFloorZ()}";
	}
}

?>