<?php
namespace Jacob\Orix\parties;

use Jacob\Orix\AdvancedPractice;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Party
{
    const LOBBY = 0;
    const IN_QUEUE = 1;
    const PLAYING = 2;
    const UNKNOWN = 3;

    const LEADER = 'Leader';
    const MEMBER = 'Member';
    /**
     * @var string|null
     */
    private ?string $leader;
    /**
     * @var string
     */
    private string $id;
    /**
     * @var array
     */
    private array $members;
    /**
     * @var int
     */
    private int $maxPlayers;
    /**
     * @var bool
     */
    private bool $public;
    /**
     * @var int
     */
    private int $status;

    /**
     * @param string|null $leader
     * @param array $members
     * @param int $maxPlayers
     * @param bool $public
     */
    public function __construct(?string $leader, array $members, int $maxPlayers, bool $public){// i remade this about 3 times XD, i think its better don't storing the player class in members array for min cpu usage.
        $this->leader = $leader;
        //$this->id = $id;
        $this->members = $members;
        // party config
        $this->maxPlayers = $maxPlayers;
        $this->public = $public;
        $this->status = self::LOBBY;
        echo('--- '.$this->getLeader());
    }

    /**
     * @return string
     */
    public function getLeader(): string
    {
        return $this->leader;
    }

    /**
     * @param string $leader
     */
    public function setLeader(string $leader): void
    {
        $this->leader = $leader;
    }

    /**
     * @return array
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @param array $members
     */
    public function setMembers(array $members): void
    {
        $this->members = $members;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    /**
     * @param int $maxPlayers
     */
    public function setMaxPlayers(int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     */
    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    // return classes

    /**
     * @return Player|null
     */
    public function getLeaderClass(): ?Player{
        $pre = AdvancedPractice::getInstance()->getServer()->getPlayerExact($this->getLeader());
        if($pre !== null){
            return $pre;
        }else{
            return null;
        }
    }

    // ---- §§§
    public function disbandParty(){//just call this function, not the PartyManager functions.
        $this->getLeaderClass()->sendMessage(TextFormat::RED.'You disbanded your party.');
        AdvancedPractice::getPartyManager()->deleteParty($this->getLeader());

        foreach ($this->getMembers() as $memberName){
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($memberName);
            if($member instanceof Player){
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setParty(null);
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyRole(null);
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyDuel(null);
                $member->sendMessage(TextFormat::RED.'The party has been disbanded.');
                $member->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveHubKit();
            }
        }
        unset($this->leader);
        unset($this->id);
        unset($this->members);
        $this->setStatus(self::UNKNOWN);
    }
    public function disbandPartyByQuit(){//just call this function, not the PartyManager functions.
        AdvancedPractice::getPartyManager()->deleteParty($this->getLeader());
        foreach ($this->getMembers() as $memberName){
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($memberName);
            if($member instanceof Player){
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setParty(null);
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyRole(null);
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyDuel(null);
                $member->sendMessage(TextFormat::RED.'The party has been disbanded.');
                $member->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveHubKit();
            }
        }
        unset($this->leader);
        unset($this->id);
        unset($this->members);
        $this->setStatus(self::UNKNOWN);
    }
    /**
     * @return bool
     */
    public function isFull(): bool{
        //return count($this->getMembers()) <= $this->getMaxPlayers();
        if(count($this->getMembers()) >= $this->getMaxPlayers()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $playerName
     * @return bool
     */
    public function isLeader($playerName): bool{
        return ($playerName === $this->getLeader());
    }
    /**
     * @return bool
     */
    public function hasMinPlayer(): bool{
        if(count($this->getMembers()) >= 3){
            return true;
        }else{
            return  false;
        }
    }

    /**
     * Adds the member to the party.
     * @param Player $member
     * @return void
     */
    public function addMember(Player $member){
        if (!isset($this->members[$member->getName()]) and $member->getName() !== $this->getLeader()){
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setParty($this);
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyRole(self::MEMBER);
            $this->members[] = $member->getName();
            $this->broadcastMessage($member->getName().'§a has joined the party.');
            $this->checkInventory($member->getName());
        }
    }

    /**
     * @param Player $member
     * @return void
     */
    public function removeMember(Player $member){
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setParty(null);
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyRole(null);
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyDuel(null);
            AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveHubKit();
            //unset($this->members[array_search($member->getName(), $this->members)]);
            unset($this->members[array_search($member->getName(), $this->members)]);

            $member->teleport(AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
            $this->broadcastMessage($member->getName().'§c has left the party.');

            AdvancedPractice::getSessionManager()->getPlayerSession($member)->setPartyDuel(null);

    }
    public function removeMemberByQuit(Player $member){
            unset($this->members[array_search($member->getName(), $this->members)]);
            $this->broadcastMessage($member->getName().'§c has left the party.');
    }

    /**
     * @param Player $newLeader
     * @return void
     */
    public function changeLeader(Player $newLeader){
            $this->setLeader($newLeader->getName());
            $newLeader->sendMessage(TextFormat::GREEN.'You are now the leader of the party.');
            $this->broadcastMessage(TextFormat::YELLOW.$newLeader->getName().TextFormat::RESET.' is the new leader of the party.');
            foreach ($this->getMembers() as $member){
                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                $lobby = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getDefaultWorld();
                if($member->getWorld() === $lobby){
                    $this->checkInventory($member->getName());
                }
            }
        //$this->getLeader()->sendMessage('[PARTY] > Party leader has been changed');
    }
    public function hotbarFunctions(Player $actioner, $id){}
    public function broadcastMessage(string $message) {
        foreach ($this->getMembers() as $memberName) {
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($memberName);
                $member->sendMessage('[PARTY] '.$message);
        }
    }

    // Inventories
    public function checkInventory($playerName){
        $player = AdvancedPractice::getInstance()->getServer()->getPlayerExact($playerName);
        if ($player instanceof Player) {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->getEffects()->clear();
            $player->setHealth($player->getMaxHealth());
            $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
            $player->getInventory()->setItem(0, ItemFactory::getInstance()->get(ItemIds::DIAMOND)->setCustomName("§r§dStart party events §r§7(Right-Click)"));
            $player->getInventory()->setItem(1, ItemFactory::getInstance()->get(ItemIds::COOKED_BEEF)->setCustomName("§r§dFight other party §r§7(Right-Click)"));
            $player->getInventory()->setItem(3, ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§r§dView party members §r§7(Right-Click)"));
            if ($this->isLeader($player->getName())) {
                $player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::REDSTONE)->setCustomName("§r§cDisband party §r§7(Right-Click)"));
            } else {
                $player->getInventory()->setItem(8, ItemFactory::getInstance()->get(ItemIds::REDSTONE)->setCustomName("§r§cLeave party §r§7(Right-Click)"));// TODO: Disband/Leave party
            }
        }
    }

    /**
     * @return Player|null
     */
    public function getSessions(){
        $member = null;
        foreach ($this->getMembers() as $eqmember){
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($eqmember);;
        }
        return $member;
    }

}