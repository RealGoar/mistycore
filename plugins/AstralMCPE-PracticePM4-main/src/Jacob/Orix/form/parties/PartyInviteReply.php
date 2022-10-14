<?php

namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\parties\PartyInvitation;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PartyInviteReply extends SimpleForm{
    public function __construct(PartyInvitation $invitation){
        parent::__construct($invitation->getParty()->getLeader()."'s party invitation.", 'Sender: '.$invitation->getSender() . $invitation->getTimeReadable().' ago');
        $acceptButton = new Button(TextFormat::GREEN.'Accept');
        $declineButtom = new Button(TextFormat::RED.'Decline');

        $this->addButton($acceptButton, function (Player $player) use($invitation){
            if($invitation->getParty()->isFull()){
                $player->sendMessage('The party is full.');
            }else{
                if(AdvancedPractice::getSessionManager()->getPlayerSession($player)->isInParty()){
                    $player->sendMessage('You already in other party.');
                }else{
                    $invitation->acceptInvitation();
                }
            }
        });
        $this->addButton($declineButtom, function() use($invitation){
            $invitation->declineInvitation();
        });
    }
}