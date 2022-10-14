<?php

namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\parties\PartyInvitation;
use pocketmine\player\Player;

class PartyMenu extends SimpleForm
{
    public function __construct(){
        parent::__construct('Choose what you want to do.');
        $this->addButton(new Button('Create a party.'), function (Player $leader){
            $leader->sendForm(new CreateParty($leader));
            $leader->sendMessage('Loading party...');
        });

            $this->addButton(new Button('Public parties.'), function (Player $player){
                if(AdvancedPractice::getPartyManager()->isPartiesCreated()){
                    $player->sendForm(new PublicParties($player));
                }else{
                    $player->sendMessage('No parties found.');
                }
            });
        $this->addButton(new Button('Party Invitations'), function (Player $player){
            if(AdvancedPractice::getPartyManager()->hasInvitations($player->getName())){
                $player->sendForm(new PartyInvitationList($player));
            }else{
                $player->sendMessage('No party invitations found.');
            }
        });

    }
}