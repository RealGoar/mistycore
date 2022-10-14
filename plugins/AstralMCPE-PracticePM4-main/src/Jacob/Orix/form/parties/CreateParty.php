<?php

namespace Jacob\Orix\form\parties;

use cosmicpe\form\CustomForm;
use cosmicpe\form\entries\custom\SliderEntry;
use cosmicpe\form\entries\custom\ToggleEntry;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\parties\Party;
use pocketmine\player\Player;

class CreateParty extends CustomForm
{
    public function __construct(Player $player)
    {
        parent::__construct('Party creation');
        $slider = new SliderEntry("Max players: \nDescription: Choose the max players you want to join in the party.", 3, 10, 1, 3);
        $private = new ToggleEntry("Public party? \nDescription: If you choose to make your party public, everyone can join.", true);

        $this->addEntry($slider);
        $this->addEntry($private);
    }
    public function handleResponse(Player $player, $data): void
    {
        if(empty($data)){
            $player->sendMessage('Party creation cancelled.');
        }else{
            AdvancedPractice::getPartyManager()->createParty($player->getName(), $data);
        }
    }
}