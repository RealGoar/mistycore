<?php

namespace Jacob\Orix\util;

use pocketmine\data\bedrock\EnchantmentIds;

class Enchantments {
	
	/** @var array[] */
	protected static array $enchantments = [
		"PROTECTION" => EnchantmentIds::PROTECTION,
		"FIRE_PROTECTION" => EnchantmentIds::FIRE_PROTECTION,
		"FEATHER_FALLING" => EnchantmentIds::FEATHER_FALLING,
		"BLAST_PROTECTION" => EnchantmentIds::BLAST_PROTECTION,
		"PROJECTILE_PROTECTION" => EnchantmentIds::PROJECTILE_PROTECTION,
		"THORNS" => EnchantmentIds::THORNS,
		"RESPIRATION" => EnchantmentIds::RESPIRATION,
		"DEPTH_STRIDER" => EnchantmentIds::DEPTH_STRIDER,
		"AQUA_AFFINITY" => EnchantmentIds::AQUA_AFFINITY,
		"SHARPNESS" => EnchantmentIds::SHARPNESS,
		"SMITE" => EnchantmentIds::SMITE,
		"BANE_OF_ARTHROPODS" => EnchantmentIds::BANE_OF_ARTHROPODS,
		"KNOCKBACK" => EnchantmentIds::KNOCKBACK,
		"FIRE_ASPECT" => EnchantmentIds::FIRE_ASPECT,
		"LOOTING" => EnchantmentIds::LOOTING,
		"EFFICIENCY" => EnchantmentIds::EFFICIENCY,
		"SILK_TOUCH" => EnchantmentIds::SILK_TOUCH,
		"UNBREAKING" => EnchantmentIds::UNBREAKING,
		"FORTUNE" => EnchantmentIds::FORTUNE,
		"POWER" => EnchantmentIds::POWER,
		"PUNCH" => EnchantmentIds::PUNCH,
		"FLAME" => EnchantmentIds::FLAME,
		"INFINITY" => EnchantmentIds::INFINITY,
		"LUCK_OF_THE_SEA" => EnchantmentIds::LUCK_OF_THE_SEA,
		"LURE" => EnchantmentIds::LURE,
		"FROST_WALKER" => EnchantmentIds::FROST_WALKER,
		"MENDING" => EnchantmentIds::MENDING,
		"BINDING" => EnchantmentIds::BINDING,
		"VANISHING" => EnchantmentIds::VANISHING,
		"IMPALING" => EnchantmentIds::IMPALING,
		"RIPTIDE" => EnchantmentIds::RIPTIDE,
		"LOYALTY" => EnchantmentIds::LOYALTY,
		"CHANNELING" => EnchantmentIds::CHANNELING,
		"MULTISHOT" => EnchantmentIds::MULTISHOT,
		"PIERCING" => EnchantmentIds::PIERCING,
		"QUICK_CHARGE" => EnchantmentIds::QUICK_CHARGE,
		"SOUL_SPEED" => EnchantmentIds::SOUL_SPEED,
	];

	public static function getEnchantments() : string {
		$keys = [];
		foreach(self::$enchantments as $name => $id){
			$keys[] = strtolower($name)." "."(".$id.")";
		}
		return implode(", ", $keys);
	}

    static public function getAll(): array {
        return self::$enchantments;
    }

}