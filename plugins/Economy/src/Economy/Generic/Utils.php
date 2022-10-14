<?php

declare(strict_types=1);

namespace Economy\Generic;

use Economy\Main;

final class Utils {
	
	public static function getDataFolder(): string {
		return Main::getInstance()->getDataFolder();
	}
}