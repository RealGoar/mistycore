<?php


namespace Jacob\Orix\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\task\TimerTask;
use Jacob\Orix\event\Event;
class EventCommand extends Command {

    /** @var AdvancedPractice */
    private $plugin;

    public function __construct(AdvancedPractice $plugin){
        parent::__construct("event", "Manage Events", "§l§7[§4!§7] - §r§7Usage: /event join|quit|create|start");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if($sender instanceof Player)
        $senderName = $sender->getName();
        if(isset($args[0])) {
            switch($args[0]) {
                case "join":
                    if(!$sender instanceof Player){
                        $sender->sendMessage(TF::RED . "Questo comando può essere usato solo in-game!");
                        return;
                    }
                    $this->getPlugin()->getTournment()->join($sender);
                    break;
                case "quit":
                    if(!$sender instanceof Player){
                        $sender->sendMessage(TF::RED . "Questo comando può essere usato solo in-game!");
                        return;
                    }
                    if($this->getPlugin()->getTournment()->inQueue($sender) or $this->getPlugin()->getTournment()->inGame($sender)) {
                        $this->getPlugin()->getTournment()->quit($sender);
                    }else{
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "You are not in a Event.");
                    }
                    break;
                case "create":
                    if ($sender->hasPermission("event.cmd") === false) {
                        $sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "[" . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "!" . TextFormat::GRAY . "] - " . TextFormat::RESET . TextFormat::GRAY . "You lack sufficient permissions to access this command.");
                        return;
                    }
                    if($sender instanceof Player)
                        if($sender->hasPermission("orixevent.cmd") === true){
                            AdvancedPractice::getSessionManager()->getPlayerSession($sender)->setOrixEventCMDCooldown();
                        }
                    if(AdvancedPractice::getSessionManager()->getPlayerSession($sender)->getOrixEventCMDCooldown() > 0){
                        $sender->sendMessage("§l§7[§4!§7] - §r§7You are still on a event cooldown that lasts 3 hours.");
                        return;
                    }
                    if($sender instanceof Player)
                        if($sender->hasPermission("heroevent.cmd") === true){
                            AdvancedPractice::getSessionManager()->getPlayerSession($sender)->setHeroEventCMDCooldown();
                        }
                    if(AdvancedPractice::getSessionManager()->getPlayerSession($sender)->getHeroEventCMDCooldown() > 0){
                        $sender->sendMessage("§l§7[§4!§7] - §r§7You are still on a event cooldown that lasts 1 day.");
                        return;
                    }
                    if(!isset($args[1])) {
                        $sender->sendMessage(TF::WHITE . "§l§7[§4!§7] - §r§7Usage: /event create {kit}");
                        return;
                    }
                    /*if(!$this->getPlugin()->existsArena($args[1])) {
                      $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "Arena Inesistente. Usa '/Event arenas' per la lista delle Arene.");
                      return false;
                    }*/
                    if(!$this->getPlugin()->existsKit($args[1])) {
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "Non-existent Kit. Use '/event kits' for the Kit list.");
                        return;
                    }
                    if(!$this->getPlugin()->getTournment()->isIdle()) {
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "There is already a Event in progress.");
                        return;
                    }
                    $this->getPlugin()->getServer()->broadcastMessage($this->getPlugin()->getPrefix() . TF::YELLOW . "Event created by " . TF::RED . $sender->getName() . TF::YELLOW . " with the Kit " . TF::RED . $args[1]);
                    $this->getPlugin()->getServer()->broadcastMessage($this->getPlugin()->getPrefix() . TF::RED . "Use '/event join' to join");
                    $this->getPlugin()->getTournment()->kit = $args[1];
                    $this->getPlugin()->getTournment()->arena = $args[1];
                    $this->getPlugin()->getTournment()->status = Event::GAME_STARTING;
                    if($sender instanceof Player) {
                        $this->getPlugin()->getTournment()->join($sender);
                    }
                    $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new TimerTask($this->getPlugin()), 20);
                    break;
                case "start":
                    if($sender->getServer()->isOp($sender)){
                        return;
                    }
                    if($this->getPlugin()->getTournment()->isStarting()) {
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::YELLOW . "Starting...");
                        $this->getPlugin()->getTournment()->start();
                    }else{
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "Non c’è nessun torneo.");
                    }
                    break;
                case "stop":
                    if($sender->getServer()->isOp($sender)){
                        return;
                    }
                    if($this->getPlugin()->getTournment()->isStarting() or $this->getPlugin()->getTournment()->isRunning()) {
                        $this->getPlugin()->getTournment()->stop();
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::YELLOW . "Event stopped.");
                    }else{
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "Non c’è nessun torneo in corso");
                    }
                    break;
                case "forcejoin":
                    if($sender->getServer()->isOp($sender)){
                        return;
                    }
                    if($this->getPlugin()->getTournment()->isStarting()) {
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::YELLOW . "Force join...");
                        foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                            $this->getPlugin()->getTournment()->join($player);
                        }
                    }else{
                        $sender->sendMessage($this->getPlugin()->getPrefix() . TF::RED . "Non c’è nessun torneo.");
                    }
                    break;
                case "setarena":
                    if(!$sender instanceof Player){
                        $sender->sendMessage(TF::RED . "Questo comando può essere usato solo in-game!");
                        return;
                    }
                    if(!$sender->getServer()->isOp($senderName)){
                        return;
                    }
                    if(!isset($args[1]) or !isset($args[2])) {
                        $sender->sendMessage(TF::RED . "Usa /Event setarena {name} duel1|duel2|spectator");
                        return;
                    }
                    if($args[2] === "duel1") {
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn1.X", $sender->getPosition()->getX());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn1.Y", $sender->getPosition()->getY());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn1.Z", $sender->getPosition()->getZ());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn1.Level", $sender->getWorld()->getFolderName());
                        $this->getPlugin()->getArenas()->save();
                        $sender->sendMessage(TF::YELLOW . "GameSpawn1 settato con successo per l’arena " . TF::RED . $args[1]);
                    }elseif($args[2] === "duel2") {
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn2.X", $sender->getPosition()->getX());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn2.Y", $sender->getPosition()->getY());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn2.Z", $sender->getPosition()->getZ());
                        $this->getPlugin()->getArenas()->setNested("$args[1].GameSpawn2.Level", $sender->getWorld()->getFolderName());
                        $this->getPlugin()->getArenas()->save();
                        $sender->sendMessage(TF::YELLOW . "GameSpawn2 settato con successo per l’arena " . TF::RED . $args[1]);
                    }elseif($args[2] === "spectator") {
                        $this->getPlugin()->getArenas()->setNested("$args[1].SpectatorSpawn.X", $sender->getPosition()->getX());
                        $this->getPlugin()->getArenas()->setNested("$args[1].SpectatorSpawn.Y", $sender->getPosition()->getY());
                        $this->getPlugin()->getArenas()->setNested("$args[1].SpectatorSpawn.Z", $sender->getPosition()->getZ());
                        $this->getPlugin()->getArenas()->setNested("$args[1].SpectatorSpawn.Level", $sender->getWorld()->getFolderName());
                        $this->getPlugin()->getArenas()->save();
                        $sender->sendMessage(TF::YELLOW . "SpectatorSpawn settato con successo per l’arena " . TF::RED . $args[1]);
                    }else{
                        $sender->sendMessage(TF::RED . "Usa /Event setarena {name} duel1|duel2|spectator");
                    }
                    break;
                case "kits":
                    $msg = TF::YELLOW . "Kits List:" . TF::EOL;
                    $kits = $this->getPlugin()->getKits()->getAll();
                    foreach($kits as $kitName => $value) {
                        $msg .= "§7» §c" . $kitName . TF::EOL;
                    }
                    $sender->sendMessage($msg);
                    break;
                case "arenas":
                    $msg = TF::YELLOW . "Arenas list:" . TF::EOL;
                    $arenas = $this->getPlugin()->getArenas()->getAll();
                    foreach($arenas as $arena => $value) {
                        $msg .= "§7» §c" . $arena . TF::EOL;
                    }
                    $sender->sendMessage($msg);
                    break;
                default:
                    $sender->sendMessage(TF::WHITE . "§l§7[§4!§7] - §r§7Usage: /event join|quit|create|start");
                    break;
            }
        }else{
            $sender->sendMessage(TF::WHITE . "§l§7[§4!§7] - §r§7Usage: /event join|quit|create|start");
        }
        return;
    }

    public function getPlugin(): AdvancedPractice {
        return $this->plugin;
    }
}
