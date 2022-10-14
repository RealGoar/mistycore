<?php

namespace Jacob\Orix\parties;

use Jacob\Orix\AdvancedPractice;
use pocketmine\utils\TextFormat;

class PartyManager
{
    /**
     * @var Party[]
     */
    private array $parties = array();
    private array $partiesQueue = array();
    private array $partiesMatch = array();
    /** @var PartyInvitation[] */
    private array $partyInvitation = array();

    public function createParty($leader, $data){
        $party = new Party($leader, [$leader], $data[0], $data[1]);
        $this->parties[$leader] = $party;
        $leaderClass = AdvancedPractice::getInstance()->getServer()->getPlayerExact($leader);
        AdvancedPractice::getSessionManager()->getPlayerSession($leaderClass)->setParty($party);
        AdvancedPractice::getSessionManager()->getPlayerSession($leaderClass)->setPartyRole(Party::LEADER);
        $this->getParty($leader)->checkInventory($leader);

        $party->getLeaderClass()->sendMessage(TextFormat::GREEN.'Successfully created the party!');
    }

    /**
     * @return Party[]
     */
    public function getParties(): array
    {
        return $this->parties;
    }
    /**
     * Checks if the party is created/exist.
     * @param string $name
     * @return bool
     */
    public function existParty(string $name): bool
    {
        return isset($this->parties[$name]);
    }
    /**
     * @return array
     */
    public function getPartiesQueue(): array
    {
        return $this->partiesQueue;
    }
    /**
     * @param array $partiesQueue
     */
    public function setPartiesQueue(array $partiesQueue): void
    {
        $this->partiesQueue = $partiesQueue;
    }
    /**
     * @return array
     */
    public function getPartiesMatch(): array
    {
        return $this->partiesMatch;
    }
    /**
     * @param array $partiesMatch
     */
    public function setPartiesMatch(array $partiesMatch): void
    {
        $this->partiesMatch = $partiesMatch;
    }
    /**
     * @param string $playerName
     * @return Party
     */
    public function getParty(string $playerName): Party
    {
        $playerClass = AdvancedPractice::getInstance()->getServer()->getPlayerExact($playerName);
        return AdvancedPractice::getSessionManager()->getPlayerSession($playerClass)->getParty();
    }
    /**
     * @param string $name
     */
    public function deleteParty(string $name): void
    {
        unset($this->parties[$name]);
    }
    /**
     * @return bool
     */
    public function isPartiesCreated(): bool
    {
        if(empty($this->getParties())){
            return false;
        }else{
            return true;
        }
    }
    /**
     * Returns an array of all party invitation classes.
     * @return PartyInvitation[]
     */
    public function getPartyInvitation(): array
    {
        return $this->partyInvitation;
    }
    /**
     * Returns all the party invitations from the player in array
     * @param string $player
     * @return PartyInvitation[]
     */
    public function getInvites(string $player):array{
        $result=[];
            foreach($this->getPartyInvitation() as $invite){
                if($invite->isTarget($player)){
                    $result[]=$invite;
                }
            }
        return $result;
    }
    public function getExactInvitation(){}

    /**
     * Check if the player has invitations. Returns bool.
     * @param string $playerName
     * @return bool
     */
    public function hasInvitations(string $playerName): bool{
            if(empty($this->getInvites($playerName))){
                return false;
            }else{
                return true;
            }
    }
    // delete party invitation of one party if it is disbanded, for no false invitations.

    /**
     * Returns all the invitations from a party, used to delete the invitation to join at a party if it is disbanded.
     * @param Party $party
     * @return array
     */
    public function invitationCameFromParty(Party $party): array{
        $result=[];
            foreach($this->getPartyInvitation() as $invite){
                if($invite->isParty($party)){
                    $result[]=$invite;
                }
            }
        return $result;
    }

    /**
     * Check if player is already invited to the party.
     * @param  string $target
     * @param Party $party
     * @return bool
     */
    public function alreadyInvited(string $target, Party $party):bool{
        $result=false;
        foreach($this->getInvites($target) as $invites){
                if($party->getLeader() === $invites->getParty()->getLeader()){
                    $result = true;
            }
        }
        return $result;
    }

    /**
     * @param Party $party
     * @param $senderName
     * @param $targetName
     * @return void
     */
    public function invitePlayer(Party $party, $senderName, $targetName){
        $invite = new PartyInvitation($party, $senderName, $targetName);
        $this->partyInvitation[] = $invite;
        $sender = AdvancedPractice::getInstance()->getServer()->getPlayerExact($senderName);
        $target = AdvancedPractice::getInstance()->getServer()->getPlayerExact($targetName);
        $sender->sendMessage("You invited ".$target->getName()." to your party.");
        $target->sendMessage($sender->getName()." invited you to their party.");
    }

    public function removeInvitation(PartyInvitation $invitation){
        unset($this->partyInvitation[array_search($invitation, $this->partyInvitation)]);
    }
}