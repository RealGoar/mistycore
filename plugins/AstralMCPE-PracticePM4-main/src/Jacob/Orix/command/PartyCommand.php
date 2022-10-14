<?php

namespace Jacob\Orix\command;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\form\parties\CreateParty;
use Jacob\Orix\form\parties\MembersList;
use Jacob\Orix\parties\Party;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;

class PartyCommand extends Command
{
    public function __construct() {
        parent::__construct("party");
        $this->description = "Party command.";
        $this->usageMessage = 'Use /party help for information about it.';
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
     if($sender instanceof Player){
         $session = AdvancedPractice::getSessionManager()->getPlayerSession($sender);
         if (empty($args)){
             $sender->sendMessage('Wrong syntax, empty args to execute.');
             $sender->sendMessage($this->getUsage());
         }else{
             switch ($args[0]){
                 case 'create':
                 case 'make':
                 case 'new':
                 case 'crear':
                     //TODO: Create party functions.
                     if($session->isInParty()){
                         $sender->sendMessage('You are already in a party.');
                     }else{
                         $sender->sendMessage('Creating party...');
                         $sender->sendForm(new CreateParty($sender));
                     }
                     break;
                 case 'manage':
                 case 'mng':
                 case 'settings':
                 case 'sets':
                     if($session->isInParty()){
                         if($session->getPartyRole() === Party::LEADER){
                             $sender->sendForm(new MembersList($sender));
                         }
                     }else{
                         $sender->sendMessage('You are not in a party.');
                     }
                     break;
                 case 'invite':
                     if($session->isInParty()){
                         if(isset($args[1])){
                             $target = AdvancedPractice::getInstance()->getServer()->getPlayerExact($args[1]);
                             if ($target === null) {
                                 $sender->sendMessage('Player not found.');
                                 return;
                             }
                             if($target->isOnline()){
                                 if(AdvancedPractice::getSessionManager()->getPlayerSession($target)->isInParty()){
                                     $sender->sendMessage('This player is in party');
                                 }else{
                                     if($session->getPartyRole() === Party::LEADER){
                                         if(AdvancedPractice::getPartyManager()->alreadyInvited($args[1], $session->getParty())){
                                             $sender->sendMessage('Player already invited to your party.');
                                         }else{
                                             $sender->sendPopup('Sending invitation...');
                                             AdvancedPractice::getPartyManager()->invitePlayer($session->getParty(), $sender->getName(), $args[1]);
                                         }
                                     }else{
                                         $sender->sendMessage('You cant invite players.');
                                     }
                                 }
                             }else{
                                 $sender->sendMessage('Player not found.');
                             }
                         }else{
                             $sender->sendMessage('Please specific a player name.');
                             //TODO: Make form for choose available player to invite.
                         }
                     }else{
                         $sender->sendMessage('You are not in a party.');
                     }
                     break;
                 default:
                     $sender->sendMessage($this->getUsage());
                     break;
             }
         }
     }elseif($sender instanceof ConsoleCommandSender){//dumb party stats
         if(!isset($args[0])){
             $sender->sendMessage('Console commands for party:
             /party <partiesCount> - []');
         }
     }
    }
}
