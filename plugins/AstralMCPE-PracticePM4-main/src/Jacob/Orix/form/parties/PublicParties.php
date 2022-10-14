<?php

namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\parties\Party;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PublicParties extends SimpleForm
{
    public function __construct(Player $player){
        parent::__construct('Public parties.');
        //this is supposed to work but :|
        //$possible = $this->jsonSerialize();

        foreach (AdvancedPractice::getPartyManager()->getParties() as $party) {
            //if ($party->isPublic() and !$party->isFull()) {
            $members = count($party->getMembers());
            $maxMembers = $party->getMaxPlayers();
            if($party->isPublic()){
                $partyButton = new Button($party->getLeader() . "'s party ($members/$maxMembers)\n".TextFormat::LIGHT_PURPLE.'[PUBLIC]');
            }else{
                $partyButton = new Button($party->getLeader() . "'s party ($members/$maxMembers) \n ".TextFormat::LIGHT_PURPLE.'[PRIVATE]');
            }
            $this->addButton($partyButton, function (Player $player) use ($party) {
                $player->sendForm(new JoinPublicParty($player, $party->getLeader()));
            });

            $partyButton = new Button('Go back.');
            $this->addButton($partyButton, function (Player $player){
                $player->sendForm(new PartyMenu());
            });
        }
    }
}
/*

        $possible = $this->jsonSerialize();
        if(empty($possible['buttons'])){
            $player->sendMessage('[PARTY] > There are not parties available to join.');
        }else{
            foreach (AdvancedPractice::getPartyManager()->getParties() as $party){
                if($party->isPublic() and !$party->isFull()){
                    $partyButton = new Button($party->getLeader()->getName()."'s party");
                    $this->addButton($partyButton, function(Player $player) use ($party) {
                        $player->sendForm(new JoinPublicParty($player, $party->getLeader()->getName()));
                    });
                }
            }
        }
 */