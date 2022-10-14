<?php
/*
 * This class is for handle all the types of party dueling system, it is a remade of the old version of single classes for each duel type.
 * Original idea of Dwifulove (Blessed David). SM: @BlesssedDavid :)
 */

namespace Jacob\Orix\duel\parties;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\generator\DefaultPartyGenerator;
use Jacob\Orix\parties\Party;
use Jacob\Orix\util\Utilities;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\WorldCreationOptions;

class PartyMatrix
{
    // partyChallenger, partyOpponent, partyType, kit,
    /*
     * partyChallenger - The party who sent a duel.
     *
     * partyOpponent - The party who accepted the duel.
     *
     * partyType - Type of dueling. (Party vs Party[PDP], Party Group Duel [PGD], Party FFA Duel[PFD]) - pdp, pgd, pfd
     *
     * partyKit - Type of kit for the duel.
     *
     * partyConfig - Array of configs for the party ['allowSpec', '']
     *
     */
    private Party $partyChallenger;
    private ?Party $partyOpponent;
    private string $partyType;
    private string $partyKit;
    private array $partyConfig;
    // other vars
    private string $worldName;
    private array $mergedPlayers;// all players of both parties.
    // party vs party
    private array $aliveChallengerMembers;
    private array $aliveOpponentMembers;
    // party group duel
    private array $aliveGroupOne;
    private array $aliveGroupTwo;
    // ffa
    private $alivePlayers;

    /**
     * @param Party $partyChallenger
     * @param Party|null $partyOpponent
     * @param string $partyType
     * @param string $partyKit
     * @param array $partyConfig
     */
    public function __construct(Party $partyChallenger, ?Party $partyOpponent, string $partyType, string $partyKit, array $partyConfig)
    {
        $this->partyChallenger = $partyChallenger;

        $this->partyType = $partyType;
        $this->partyKit = $partyKit;
        $this->partyConfig = $partyConfig;
        $this->worldName = 'party'.str_shuffle('123456789abcdefghijklmnopqrstuvwxyz');
        //load world
        // map config
        $world = AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName($this->worldName);
        switch ($this->getPartyKit()){
            case 'builduhc':
                mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "partydbuhcarena", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                break;
            case 'gapple':
                mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "partygapplearena", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                break;
            case 'nodebuff':
                mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "partynodebuffarena", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                break;
            default:
                $options = new WorldCreationOptions();
                $options->setGeneratorClass(DefaultPartyGenerator::class);
                $options->setSeed(0);
                $options->setSpawnPosition(new Vector3(0,100,0));
                AdvancedPractice::getInstance()->getServer()->getWorldManager()->generateWorld($this->worldName, $options);
                break;
        }
        if(is_null($partyOpponent)){
            $this->partyOpponent = null;
            switch ($this->partyType){
                case 'pfd'://ffa
                case 'PFD':
                    $this->alivePlayers = $partyChallenger->getMembers();
                AdvancedPractice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($partyChallenger, $world): void {
                    switch ($this->getPartyKit()){
                        case 'builduhc':
                            foreach ($partyChallenger->getMembers() as $member){
                                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                                $member->teleport(new Location(0, 65, 0, $world, 0, 0));
                                $member->sendMessage('Kit: Build UHC');
                                $member->sendMessage('Duel started!');
                                AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveBuildUHCKit();
                            }
                            break;
                        case 'gapple':
                            foreach ($partyChallenger->getMembers() as $member) {
                                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                                $member->teleport(new Location(0, 65, 0, $world, 0, 0));
                                $member->sendMessage('Kit: Gapple');
                                $member->sendMessage('Duel started!');
                                AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveBuildUHCKit();
                            }
                            break;
                        case 'nodebuff':
                            foreach ($partyChallenger->getMembers() as $member) {
                                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                                $member->teleport(new Location(0, 65, 0, $world, 0, 0));
                                $member->sendMessage('Kit: Nodebuff');
                                $member->sendMessage('Duel started!');
                                AdvancedPractice::getSessionManager()->getPlayerSession($member)->giveBuildUHCKit();
                            }
                            break;
                    }
                }), 20);
                    break;

                case 'pgd'://groups
                case 'PGD':
                $shuff = $partyChallenger->getMembers();
                $leng = count($shuff);
                shuffle($shuff);

                $this->aliveGroupOne = array_splice($shuff, $leng/2);
                $this->aliveGroupTwo = array_splice($shuff, $leng/2);
                switch ($this->getPartyKit()){
                    case 'builduhc':
                        foreach ($this->aliveGroupOne as $groupOne){
                            $groupOne = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupOne);
                            $groupOne->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupOne->sendMessage('Kit: Build UHC');
                            $groupOne->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupOne)->giveBuildUHCKit();
                        }
                        foreach ($this->aliveGroupTwo as $groupTwo){
                            $groupTwo = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupTwo);
                            $groupTwo->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupTwo->sendMessage('Kit: Build UHC');
                            $groupTwo->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupTwo)->giveBuildUHCKit();
                        }
                        break;
                    case 'gapple':
                        foreach ($this->aliveGroupOne as $groupOne){
                            $groupOne = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupOne);
                            $groupOne->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupOne->sendMessage('Kit: Gapple');
                            $groupOne->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupOne)->giveBuildUHCKit();
                        }
                        foreach ($this->aliveGroupTwo as $groupTwo){
                            $groupTwo = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupTwo);
                            $groupTwo->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupTwo->sendMessage('Kit: Gapple');
                            $groupTwo->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupTwo)->giveBuildUHCKit();
                        }
                        break;
                    case 'nodebuff':
                        foreach ($this->aliveGroupOne as $groupOne){
                            $groupOne = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupOne);
                            $groupOne->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupOne->sendMessage('Kit: Nodebuff');
                            $groupOne->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupOne)->giveBuildUHCKit();
                        }
                        foreach ($this->aliveGroupTwo as $groupTwo){
                            $groupTwo = AdvancedPractice::getInstance()->getServer()->getPlayerExact($groupTwo);
                            $groupTwo->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $groupTwo->sendMessage('Kit: Nodebuff');
                            $groupTwo->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($groupTwo)->giveBuildUHCKit();
                        }
                        break;
                }
                    break;
            }
        }else{
            // party vs party
            $this->partyOpponent = $partyOpponent;
            AdvancedPractice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($partyChallenger, $partyOpponent, $world): void {
                $challengerMembers = $partyChallenger->getMembers();
                $opponentMembers = $partyOpponent->getMembers();
                switch ($this->getPartyKit()){
                    case 'builduhc'://TODO: Change to real Position for each kit.
                        foreach ($challengerMembers as $challengerMember){
                            $challengerMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($challengerMember);
                            $challengerMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $challengerMember->sendMessage('Kit: Build UHC');
                            $challengerMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($challengerMember)->giveBuildUHCKit();
                        }
                        foreach ($opponentMembers as $opponentMember){
                            $opponentMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($opponentMember);
                            $opponentMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $opponentMember->sendMessage('Kit: Build UHC');
                            $opponentMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($opponentMember)->giveBuildUHCKit();
                        }
                        break;
                    case 'gapple':
                        foreach ($challengerMembers as $challengerMember){
                            $challengerMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($challengerMember);
                            $challengerMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $challengerMember->sendMessage('Kit: Gapple');
                            $challengerMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($challengerMember)->giveBuildUHCKit();
                        }
                        foreach ($opponentMembers as $opponentMember){
                            $opponentMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($opponentMember);
                            $opponentMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $opponentMember->sendMessage('Kit: Gapple');
                            $opponentMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($opponentMember)->giveBuildUHCKit();
                        }
                        break;
                    case 'nodebuff':
                        foreach ($challengerMembers as $challengerMember){
                            $challengerMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($challengerMember);
                            $challengerMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $challengerMember->sendMessage('Kit: Nodebuff');
                            $challengerMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($challengerMember)->giveBuildUHCKit();
                        }
                        foreach ($opponentMembers as $opponentMember){
                            $opponentMember = AdvancedPractice::getInstance()->getServer()->getPlayerExact($opponentMember);
                            $opponentMember->teleport(new Location(0, 65, 0, $world, 0, 0));
                            $opponentMember->sendMessage('Kit: Nodebuff');
                            $opponentMember->sendMessage('Duel started!');
                            AdvancedPractice::getSessionManager()->getPlayerSession($opponentMember)->giveBuildUHCKit();
                        }
                        break;
                }
            }), 20);
        }

    }
    /**
     * @return Party
     */
    public function getPartyChallenger(): Party
    {
        return $this->partyChallenger;
    }
    /**
     * @return Party
     */
    public function getPartyOpponent(): Party
    {
        return $this->partyOpponent;
    }

    /**
     * @return string
     */
    public function getPartyType(): string
    {
        return $this->partyType;
    }
    /**
     * @return string
     */
    public function getPartyKit(): string
    {
        return $this->partyKit;
    }
    /**
     * @return array
     */
    public function getPartyConfig(): array
    {
        return $this->partyConfig;
    }

    /**
     * @return array
     */
    public function getAliveGroupOne(): array
    {
        return $this->aliveGroupOne;
    }

    /**
     * @return array
     */
    public function getAliveGroupTwo(): array
    {
        return $this->aliveGroupTwo;
    }

    /**
     * @return array
     */
    public function getAlivePlayers(): array
    {
        return $this->alivePlayers;
    }

    // gameplay functions
    public function broadcastMessage(string $message){
        foreach ($this->getPartyChallenger()->getMembers() as $member){
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
            if($member->isOnline()){
                $member->sendMessage($message);
            }
        }
        if($this->getPartyType() === 'pdp'){
            foreach ($this->getPartyOpponent()->getMembers() as $member){
                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                if($member->isOnline()){
                    $member->sendMessage($message);
                }
            }
        }
    }
    public function broadcastPopup(string $message){
        foreach ($this->getPartyChallenger()->getMembers() as $member){
            $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
            if($member->isOnline()){
                $member->sendPopup($message);
            }
        }
        if($this->getPartyType() === 'pdp'){
            foreach ($this->getPartyOpponent()->getMembers() as $member){
                $member = AdvancedPractice::getInstance()->getServer()->getPlayerExact($member);
                if($member->isOnline()){
                    $member->sendMessage($message);
                }
            }
        }
    }
    // event handlers
    public function handleQuitEvent(PlayerQuitEvent $event){

    }

    public function endGame(){
        switch ($this->getPartyType()){
            case 'pgd':
            case 'PGD':
                // party group duel functions.
                $this->broadcastPopup('Duel finished!');
                break;
            case 'pfd':
            case 'PFD':
                // party ffa duel functions.
                $this->broadcastPopup('Duel finished!');
                break;
            case 'pdp':
            case 'PDP':
                // party vs party functions.
                $this->broadcastPopup('Duel finished!');
                break;
        }
    }
}