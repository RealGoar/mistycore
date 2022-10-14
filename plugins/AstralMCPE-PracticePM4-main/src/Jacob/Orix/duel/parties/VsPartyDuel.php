<?php
/*
 * This class is for handle the mode: Party vs Party.
 */
namespace Jacob\Orix\duel\parties;

use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\parties\Party;
use Jacob\Orix\util\Utilities;
use pocketmine\world\WorldCreationOptions;

class VsPartyDuel
{
    /** @var Party  */
    private Party $firstParty;
    /** @var Party  */
    private Party $secondParty;
    /** @var array  */
    private array $data;
    //game data
    private $killed = array();
    private $spectators = array();
    private $alive = array();
    private string $worldName;

    public function __construct(Party $firstParty, Party $secondParty, array $data){//Internally, first party it's the challenger. Data: (mode, spectating)
        $this->firstParty = $firstParty;
        $this->secondParty = $secondParty;
        $this->data = $data;
        $this->worldName = 'party'.str_shuffle('123456789abcdefghijklmnopqrstuvwxyz');
        $options = new WorldCreationOptions();
        switch ($this->getMode()){
            case 'buhc':
                //firsts
                AdvancedPractice::getSessionManager()->getPlayerSession($firstParty->getSessions())->setPartyDuelClass($this);
                AdvancedPractice::getSessionManager()->getPlayerSession($firstParty->getLeaderClass())->setPartyDuelClass($this);
                //sec
                AdvancedPractice::getSessionManager()->getPlayerSession($secondParty->getSessions())->setPartyDuelClass($this);
                AdvancedPractice::getSessionManager()->getPlayerSession($secondParty->getLeaderClass())->setPartyDuelClass($this);
                mkdir(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
               // Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "party-buhc", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                Utilities::recursiveCopy(AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "partydefaultarena", AdvancedPractice::getInstance()->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . "$this->worldName");
                break;
        }
    }

    /**
     * @return Party
     */
    public function getFirstParty(): Party
    {
        return $this->firstParty;
    }

    /**
     * @param Party $firstParty
     */
    public function setFirstParty(Party $firstParty): void
    {
        $this->firstParty = $firstParty;
    }

    /**
     * @return Party
     */
    public function getSecondParty(): Party
    {
        return $this->secondParty;
    }

    /**
     * @param Party $secondParty
     */
    public function setSecondParty(Party $secondParty): void
    {
        $this->secondParty = $secondParty;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getKilled(): array
    {
        return $this->killed;
    }

    /**
     * @param array $killed
     */
    public function setKilled(array $killed): void
    {
        $this->killed = $killed;
    }

    /**
     * @return array
     */
    public function getSpectators(): array
    {
        return $this->spectators;
    }

    /**
     * @param array $spectators
     */
    public function setSpectators(array $spectators): void
    {
        $this->spectators = $spectators;
    }

    /**
     * @return array
     */
    public function getAlive(): array
    {
        return $this->alive;
    }

    /**
     * @param array $alive
     */
    public function setAlive(array $alive): void
    {
        $this->alive = $alive;
    }

    // ---- game functions ----
    public function isSpec(){
        //$bool = is_bool($this->getData()['spec']);
        if($this->getData()['spec'] === true){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return array
     */
    public function getMode(){
        //$bool = is_bool($this->getData()['spec']);
        return $this->getData()['mode'];
    }




}