<?php

namespace Jacob\Orix\form\parties;

use Jacob\Orix\parties\PartyInterface;

class StatusParty implements PartyInterface
{

    function getLeader()
    {
        // TODO: Implement getLeader() method.
    }

    function getMembers()
    {
        // TODO: Implement getMembers() method.
    }

    function getMembersCount()
    {
        // TODO: Implement getMembersCount() method.
    }

    function getMaxSize()
    {
        // TODO: Implement getMaxSize() method.
    }

    function getPremium()
    {
        // TODO: Implement getPremium() method.
    }
    /*
     public function getId(): int{
        return $this->id;
    }
    public function getName(): string{
        return $this->owner;
    }
    public function setOwner($p): void{
        $this->owner = $this->getPlayerName($p);
    }
    public function getOwner(): string{
        return $this->owner;
    }
    public function isFull(): bool{
        return $this->getMemberCount() == self::PARTY_LIMIT;
    }
    public function getMemberCount(): int{
        return count($this->members);
    }
    public function getPartyOwner(): Player{
        return $this->getPlugin()->getServer()->getPlayerExact($this->owner);
    }
    public function isMemberExists($m): bool{
        return in_array($this->getPlayerName($m), $this->members);
    }
    public function isOwner($m): bool{
        return $this->getOwner() == $this->getPlayerName($m);
    }
     */
}