<?php

namespace Jacob\Orix\event;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat as TF;
use Jacob\Orix\AdvancedPractice;

class Event {

    const GAME_IDLE = 0;
    const GAME_STARTING = 1;
    const GAME_RUNNING = 2;

    /** @var int $status */
    public $status = self::GAME_IDLE;
    /** @var string $kit */
    public $kit = "";
    /** @var string $arena */
    public $arena = "";
    /** @var array $inQueuePlayers */
    public $inQueuePlayers = [];
    /** @var array $inGamePlayers */
    public $inGamePlayers = [];
    /** @var array $spectatorPlayers */
    public $spectatorPlayers = [];

    public function __construct(AdvancedPractice $plugin){
        $this->plugin = $plugin;
        $this->startCountdown = $this->getPlugin()->getStartCountdown();
        $this->duelCountdown = $this->getPlugin()->getDuelCountdown();
    }

    public function tick() {
        if($this->isStarting()) {
            if(count($this->getQueuePlayers()) > 1) {
                if($this->startCountdown > 5) {
                    $this->broadcastPopup(TF::RED . "Tournament start in " . TF::YELLOW . $this->startCountdown . TF::RED . " seconds");
                }
                if($this->startCountdown <= 5) {
                    $this->broadcastTitle(TF::RED . "Start: " . TF::YELLOW . $this->startCountdown . "...");
                }
                if($this->startCountdown == 0) {
                    $this->start();
                }
                $this->startCountdown--;
            }else{
                $this->broadcastPopup(TF::RED . "Insufficient players to start the countdown");
            }
        }
        //Duelli
        if($this->isRunning() and count($this->getGamePlayers()) === 0) {
            if($this->duelCountdown == 0) {
                $this->startDuel();
                $this->duelCountdown = $this->getPlugin()->getDuelCountdown();
            }
            $this->duelCountdown--;
            $this->broadcastPopup(TF::RED . "Duel start in: " . TF::YELLOW . $this->duelCountdown . "...");
        }elseif($this->isRunning() and count($this->getGamePlayers()) === 1) {
            $winner = reset($this->inGamePlayers);
            $this->restartWinner($winner);
        }
    }

    public function join(Player $player) {
        if($this->isStarting()) {
            if(!$this->inQueue($player)) {
                $this->inQueuePlayers[] = $player;
                $this->broadcastMessage($this->getPlugin()->getPrefix() . $player->getName() . "is joined in the tournament! Players: " . $this->countAllPlayers());
            }else{
                $player->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "You are already joining in the tournament.");
            }
        }else{
            $player->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "There is no tournament in which to join!");
        }
    }

    public function quit(Player $player, $death = false) {
        if($death and $this->inGame($player)) {
            $this->broadcastMessage($this->getPlugin()->getPrefix() . $player->getName() . " is dead.");
        }else{
            $this->broadcastMessage($this->getPlugin()->getPrefix() . $player->getName() . " has left the tournament! Players: " . $this->countAllPlayers());
        }
        $this->closePlayer($player);
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
    }

    public function addSpectator(Player $player) {
        $spawn = $this->getPlugin()->getArenas()->get($this->arena);
        $player->teleport(new Position(19025, 120, 19025, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")));
        $this->spectatorPlayers[] = $player;
        $player->setGamemode(GameMode::SPECTATOR());
        $player->getInventory()->clearAll();
    }

    public function start() {
        if(count($this->getQueuePlayers()) >= 2) {
            $this->broadcastMessage($this->getPlugin()->getPrefix() . TF::YELLOW . "The tournment has started!");
            $spawn = $this->getPlugin()->getArenas()->get($this->arena);
            foreach($this->getQueuePlayers() as $player) {
                $player->getInventory()->clearAll();
                $player->setGamemode(GameMode::SURVIVAL());
                $player->setHealth($player->getMaxHealth());
                $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                $player->teleport(new Position(19025, 120, 19025, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")));
            }
            $this->status = self::GAME_RUNNING;
        }else{
            $this->broadcastMessage($this->getPlugin()->getPrefix() . TF::RED . "There are not enough players to start the tournament.");
            $this->stop();
        }
    }

    public function startDuel() {
        $spawn = $this->getPlugin()->getArenas()->get($this->arena);
        $random_p = array_rand($this->inQueuePlayers, 2);
        shuffle($random_p);
        $player1 = $this->inQueuePlayers[$random_p[0]];
        $player2 = $this->inQueuePlayers[$random_p[1]];
        $this->inGamePlayers[] = $player1;
        $this->inGamePlayers[] = $player2;
        unset($this->inQueuePlayers[array_search($player1, $this->getQueuePlayers(), true)]);
        unset($this->inQueuePlayers[array_search($player2, $this->getQueuePlayers(), true)]);
        $player1->teleport(new Position(19025, 90, 19020, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")));
        $player2->teleport(new Position(19025, 90, 19030, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")));
        $this->getPlugin()->addKit($player1, $this->kit);
        $this->getPlugin()->addKit($player2, $this->kit);
        $this->broadcastMessage($this->getPlugin()->getPrefix() . TF::YELLOW . $player1->getName() . TF::RED . " vs " . TF::YELLOW . $player2->getName());
        $this->broadcastMessage($this->getPlugin()->getPrefix() . TF::RED . "Players: " . TF::YELLOW . $this->countAllPlayers());
    }

    public function restartWinner($player) {
        if(count($this->getQueuePlayers()) >= 1) {
            $spawn = $this->getPlugin()->getArenas()->get($this->arena);
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->setHealth($player->getMaxHealth());
            $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
            $player->teleport(new Position(19025, 120, 19025, AdvancedPractice::getInstance()->getServer()->getWorldManager()->getWorldByName("map3")));
            unset($this->inGamePlayers[array_search($player, $this->getGamePlayers(), true)]);
            $this->inQueuePlayers[] = $player;
        }else{
            $this->closePlayer($player);
            $this->stop();
            $this->getPlugin()->getServer()->broadcastMessage($this->getPlugin()->getPrefix() . $player->getName() . " has won a tournment!");
            AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
        }
    }

    public function closePlayer(Player $player) {
        if($this->inGame($player)) {
            unset($this->inGamePlayers[array_search($player, $this->getGamePlayers(), true)]);
        }
        if($this->inQueue($player)) {
            unset($this->inQueuePlayers[array_search($player, $this->getQueuePlayers(), true)]);
        }
        if($this->isSpectator($player)) {
            unset($this->spectatorPlayers[array_search($player, $this->getSpectatorPlayers(), true)]);
        }
        $player->setGamemode(GameMode::SURVIVAL());
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        $player->getEffects()->clear();
        $player->teleport($this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
        AdvancedPractice::getSessionManager()->getPlayerSession($player)->giveHubKit();
    }

    public function stop() {
        foreach($this->getSpectatorPlayers() as $player) {
            $this->closePlayer($player);
        }
        $this->status = self::GAME_IDLE;
        $this->inGamePlayers = [];
        $this->inQueuePlayers = [];
        $this->spectatorPlayers = [];
        $this->startCountdown = $this->getPlugin()->getStartCountdown();
        $this->duelCountdown = $this->getPlugin()->getDuelCountdown();
    }

    public function getQueuePlayers() {
        return $this->inQueuePlayers;
    }

    public function getGamePlayers() {
        return $this->inGamePlayers;
    }

    public function getSpectatorPlayers() {
        return $this->spectatorPlayers;
    }

    public function countAllPlayers() {
        $players = count($this->getQueuePlayers()) + count($this->getGamePlayers());
        return $players;
    }

    public function inQueue(Player $player) {
        return in_array($player, $this->getQueuePlayers(), true);
    }

    public function inGame(Player $player) {
        return in_array($player, $this->getGamePlayers(), true);
    }

    public function isSpectator(Player $player) {
        return in_array($player, $this->getSpectatorPlayers(), true);
    }

    public function isIdle(): bool {
        return $this->status === self::GAME_IDLE;
    }

    public function isStarting(): bool {
        return $this->status === self::GAME_STARTING;
    }

    public function isRunning(): bool {
        return $this->status === self::GAME_RUNNING;
    }

    public function broadcastMessage(string $msg) {
        $this->sendMessage($msg, $this->getQueuePlayers());
        $this->sendMessage($msg, $this->getGamePlayers());
        $this->sendMessage($msg, $this->getSpectatorPlayers());
    }

    public function broadcastPopup(string $msg) {
        $this->sendPopup($msg, $this->getQueuePlayers());
        $this->sendPopup($msg, $this->getGamePlayers());
    }

    public function broadcastTitle(string $msg) {
        $this->sendTitle($msg, $this->getQueuePlayers());
        $this->sendTitle($msg, $this->getGamePlayers());
    }

    public function sendMessage(string $text, $recipients = null){
        if($recipients === null){
            $recipients = AdvancedPractice::getInstance()->getServer()->getOnlinePlayers();
        }
        foreach ($recipients as $recipient)
            $recipient->sendMessage($text);
    }

    public function sendPopup(string $text, $recipients = null){
        if($recipients === null){
            $recipients = AdvancedPractice::getInstance()->getServer()->getOnlinePlayers();
        }
        foreach ($recipients as $recipient)
            $recipient->sendPopup($text);
    }

    public function sendTitle(string $text, $recipients = null){
        if($recipients === null){
            $recipients = AdvancedPractice::getInstance()->getServer()->getOnlinePlayers();
        }
        foreach ($recipients as $recipient)
            $recipient->sendTitle($text);
    }

    public function getPlugin(): AdvancedPractice {
        return $this->plugin;
    }
}
