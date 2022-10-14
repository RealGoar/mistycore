<?php
/*-----------------------------------------------------------------------------
 Copyright (c) 2022. This class of code is original code by $user, can follow @BlesssedDavid
 -----------------------------------------------------------------------------*/

namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;

class MembersList extends SimpleForm{
    private Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
        parent::__construct('Member list.');

        // ---
        $session = AdvancedPractice::getSessionManager()->getPlayerSession($this->player);
        foreach ($session->getParty()->getMembers() as $member){
            $memberButtom = new Button($member);

            $this->addButton($memberButtom, function (Player $player) use($member){
                if($player->getName() === $member){
                    $this->player->sendMessage('You cant self manage your profile,');// Maybe you can change this by storing the members array and remove the leader string.
                }else{
                    $player->sendForm(new PartyManageMembers($player, $member));
                }
            });
        }

    }
}