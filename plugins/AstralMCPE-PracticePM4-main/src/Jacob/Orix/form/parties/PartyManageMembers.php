<?php
namespace Jacob\Orix\form\parties;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;

class PartyManageMembers extends SimpleForm {
    public function __construct(Player $leader, $memberName)
    {
        parent::__construct("Member Manage", "What you want to do with this member?");

        //buttoms
        $kick = new Button('Kick member from party.');
        $promote = new Button('Promote member to leader.');
        $this->addButton($kick,function (Player $leader) use ($memberName){
            $leaderParty = AdvancedPractice::getSessionManager()->getPlayerSession($leader)->getParty();
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($memberName);
            $leaderParty->removeMember($member);
            $member->sendMessage('You have been kicked from the party.');
        });
        $this->addButton($promote, function (Player $leader) use ($memberName){
            $memberClass = AdvancedPractice::getInstance()->getServer()->getPlayerExact($memberName);
            $leaderParty = AdvancedPractice::getSessionManager()->getPlayerSession($leader)->getParty();
            $leaderParty->changeLeader($memberClass);
            $leader->sendMessage('You changed the party leader.');
        });
    }
}