<?php
namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;

class PartyInvitationList extends SimpleForm{
    public function __construct(Player $player){
        parent::__construct('Party Invitations.');

        foreach (AdvancedPractice::getPartyManager()->getInvites($player->getName()) as $invitation){
            $this->addButton(new Button($invitation->getParty()->getLeader()."'s Party \n Sender: ".$invitation->getSender()), function () use ($player, $invitation){
                if($invitation->getParty()->isFull()){
                    $player->sendMessage('The party is full');
                }else{
                    $player->sendForm(new PartyInviteReply($invitation));
                }
            });
        }
    }
}