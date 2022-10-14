<?php
namespace Jacob\Orix\parties;


use Jacob\Orix\AdvancedPractice;
use pocketmine\player\Player;

class PartyInvitation
{
    /**
     * @var Party
     */
    private Party $party;
    /**
     * @var string
     */
    private string $sender;
    /**
     * @var string
     */
    private string $target;
    /**
     * @var float
     */
    private float $time;

    /**
     * @param Party $party
     * @param string $sender
     * @param string $target
     */
    public function __construct(Party $party, string $sender, string $target)
    {
        $this->party = $party;
        $this->sender = $sender;
        $this->target = $target;
        $this->time = time();
    }

    /**
     * @return Party
     */
    public function getParty(): Party
    {
        return $this->party;
    }

    /**
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }
    public function isTarget($playerName): bool
    {
        return $this->target == $playerName;
    }
    public function isSender($playerName): bool
    {
        return $this->sender == $playerName;
    }
    public function getTime(): float
    {
        return $this->time;
    }
    function getTimeReadable()
    {
        $org = $this->getTime();
        $h = floor($org / 3600);
        $org -= $h * 3600;
        $m = floor($org / 60);
        $org -= $m * 60;
        return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $org);
    }
    public function isParty($partyName): bool
    {
        return $this->sender == $partyName;
    }
    public function reset(){
        AdvancedPractice::getPartyManager()->removeInvitation($this);
    }

    public function acceptInvitation(){
        $target = AdvancedPractice::getInstance()->getServer()->getPlayerExact($this->target);
        $sender = AdvancedPractice::getInstance()->getServer()->getPlayerExact($this->sender);
        //if($target !== null and $sender !== null){
        if(!is_null($target) || !is_null($sender)){
            if(AdvancedPractice::getPartyManager()->existParty($this->party->getLeader())){
                $this->party->addMember($target);
                $sender->sendMessage($target->getName().' has accepted the invitation.');
            }else{
                    $target->sendMessage('This party has been disbanded, no longer available.');
            }
        }
        $this->reset();
    }
    public function declineInvitation(){
        $target = AdvancedPractice::getInstance()->getServer()->getPlayerExact($this->target);
        $sender = AdvancedPractice::getInstance()->getServer()->getPlayerExact($this->sender);
        //if($target !== null and $sender !== null){
        if(!is_null($target) || !is_null($sender)){
                $target->sendMessage('You decline the'.$sender->getName()."'s party invitation.");
            //$sender->sendMessage($target->getName().' has decline your party invitation.'); - I think it is better not to show the decline message. :)

            $this->reset();
        }
    }
}
