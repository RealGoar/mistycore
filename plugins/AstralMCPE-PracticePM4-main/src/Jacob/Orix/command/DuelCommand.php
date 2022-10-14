<?php


namespace Jacob\Orix\command;


use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\duel\Duel;
use Jacob\Orix\form\DuelPlayerForm;
use Jacob\Orix\form\DuelListForm;
use Jacob\Orix\session\PlayerSession;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DuelCommand extends Command

{

    //public static $modes = ["nodebuff", "dragon", "gapple", "ability", "builduhc", "soup", "sumo", "knockback", "baseraiding", "base-raiding", "saferoom", "safe-room"];
    
    public function __construct() {
        parent::__construct("duel");
        $this->description = "Duel Players of your choice.";
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($sender instanceof Player) {
            if (count($args) > 0) {
                if(!isset($args[0])){
                    $sender->sendMessage("§l§7[§d!§7] - §r§7Usage: /duel {player}");
                    return;
                }
                
                $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                if($player === null){
                    $sender->sendMessage("§l§7[§d!§7] - §r§7This player does not exist.");
                    return;
                }
                if ($player === $sender) {
                    $sender->sendMessage("§l§7[§d!§7] - §r§7You cannot duel yourself!");
                    return;
                }
                if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getDuelClass() === true) {
                    $sender->sendMessage("§l§7[§d!§7] - §r§7This player is already in a duel.");
                    return;
                }
                $sender->sendForm(new DuelListForm($player));
                
               // $modes = array_merge(AdvancedPractice::getInstance()->modes, AdvancedPractice::getInstance()->newModes);
                
               /* if(in_array($args[1], $modes)) {
                    $player->sendForm(new InviteForm($sender, $args[0], $args[1]));
                } else {
                    foreach (self::$modes as $mode) {
                        $sender->sendMessage("§l§7[§d!§7] - §r§7The mode you have entered is not a mode. List of modes: $mode");
                    }
                }*/
            }
        }
    }
}