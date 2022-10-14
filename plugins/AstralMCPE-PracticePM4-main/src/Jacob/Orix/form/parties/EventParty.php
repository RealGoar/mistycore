<?php

namespace Jacob\Orix\form\parties;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\event\Event;
use Jacob\Orix\task\PartyTimerTask;
use Jacob\Orix\task\TimerTask;
use pocketmine\player\Player;

class EventParty extends \dktapps\pmforms\CustomForm
{
    public function __construct()
    {
        parent::__construct('Party creation', [
            new Dropdown('Kit', 'Kit', ['Gapple', 'Soup', 'Ability', 'Sumo', 'NoDebuff'])
        ], function(Player $player, CustomFormResponse $response) : void{
            $data = $response->getAll();

            if (count($data) == 0)
                return;

            $session = AdvancedPractice::getSessionManager()->getPlayerSession($player);

            if (!$session->isInParty())
                return;

            $party = $session->getParty();

            if (!$party->isLeader($player->getName()))
                return;

            $event = AdvancedPractice::getInstance()->pEvents[$party->getLeader()] = new Event(AdvancedPractice::getInstance());
            $event->kit = $data[0] ?? 'NoDebuff';
            $event->arena = $data[0] ?? 'NoDebuff';
            $event->status = Event::GAME_STARTING;
            foreach ($party->getMembers() as $playerName) {
                $player = AdvancedPractice::getInstance()->getServer()->getPlayerExact($playerName);
                if ($player !== null && $player->isOnline()) $event->join($player);
            }
            AdvancedPractice::getInstance()->getScheduler()->scheduleRepeatingTask(new PartyTimerTask($event, AdvancedPractice::getInstance()), 20);
        });
    }
}
