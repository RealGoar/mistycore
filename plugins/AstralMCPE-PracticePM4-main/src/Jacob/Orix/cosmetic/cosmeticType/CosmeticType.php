<?php namespace Jacob\Orix\cosmetic\cosmeticType;

abstract class CosmeticType {

    abstract function getName() : string;

    abstract function isKillsNeeded() : bool;

    abstract function isPermissionNeeded() : bool;

    abstract function getPermission() : string;

    abstract function getKillsNeeded() : int;

}