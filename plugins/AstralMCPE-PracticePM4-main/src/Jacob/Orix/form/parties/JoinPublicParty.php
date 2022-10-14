<?php

namespace Jacob\Orix\form\parties;

use cosmicpe\form\ModalForm;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class JoinPublicParty extends ModalForm
{

    private $partyName;
    public function __construct(Player $player, $partyName){
        $this->partyName = $partyName;
        parent::__construct('Do you want to join this party?');
        $this->setFirstButton(TextFormat::LIGHT_PURPLE.'Yes');
        $this->setSecondButton(TextFormat::RED.'No');
    }
    protected function onAccept(Player $player): void
    {
        $party = AdvancedPractice::getPartyManager()->getParty($this->partyName);
        if($party->isPublic()){
            if($party->isFull()){
                $player->sendMessage('The party is full.');
            }else{
                $player->sendMessage('Joining in the party...');
                $party->addMember($player);
            }
        }else{
            if(AdvancedPractice::getPartyManager()->alreadyInvited($player->getName(), $party)){
                $party->addMember($player);

            }
            $player->sendMessage('This party is private, you need an invitation.');
        }

    }

    protected function onClose(Player $player): void
    {
        $player->sendForm(new PartyMenu());
    }

}