<?php

namespace Jacob\Orix\command\staff;

use pocketmine\data\bedrock\EnchantmentIdMap;
use Jacob\Orix\AdvancedPractice;

use SystemBan\utils\Enchantments;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\TextFormat as TE;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

class EnchantCommand extends Command {
	
	/**
     * EnchantCommand Constructor.
     */
    public function __construct(){
        $this->setPermission("enchant.command.use");
        parent::__construct("enchant");
    }
	
	/**
     * @param CommandSender $sender
     * @param String $label
     * @param array $args
     * @return void
     */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(!$this->testPermission($sender)){
			return;
        }
        if($args[0] === "list"){
        	$enchants = Enchantments::getEnchantments();
        	$sender->sendMessage(TE::AQUA."Enchantments".TE::WHITE.": ".TE::RESET.$enchants);
        	return;
        }
        if(empty($args[0])||empty($args[1])){
        	$sender->sendMessage(TE::LIGHT_PURPLE."Use: /{$label} [string: playerName] [string: enchantmentName] [int: enchantmentLevel]");
        	return;
        }
        $player = AdvancedPractice::getInstance()->getServer()->getPlayerByPrefix($args[0]);
        if(!$player instanceof Player){
        	$sender->sendMessage(TE::LIGHT_PURPLE."The player you are logged in is not connected!");
        	return;
        }
        $item = $player->getInventory()->getItemInHand();
        if($item->isNull()){
        	$sender->sendMessage(TE::LIGHT_PURPLE."You must have an item in hand to use this");
        	return;
        }
        $enchantment = null;
        if(is_numeric($args[1])){
			$enchantment = EnchantmentIdMap::getInstance()->fromId((int) $args[1]);
		}else{
            foreach(Enchantments::getAll() as $name => $id) {
                if(strtoupper($args[1]) === $name) {
                    $enchantment = EnchantmentIdMap::getInstance()->fromId((int) $id);
                }
            }
		}
        if(!($enchantment instanceof Enchantment)){
        	$sender->sendMessage(TE::LIGHT_PURPLE."The enchantment you are entering does not exist");
        	return;
        }
        $level = 1;
        if(!empty($args[2])){
        	$level = $args[2];
        }
        $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
        $player->getInventory()->setItemInHand($item);
    }
}