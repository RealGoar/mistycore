<?php

namespace fayu\cmd;

use fayu\Main;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\utils\TextFormat;

class AlertCommand extends VanillaCommand {

    public function __construct()
    {
        parent::__construct("alert", "Enviar mensaje en el servidor de Discord y Minecraft");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(empty($args[0])) {
            $sender->sendMessage(TextFormat::colorize("&cFalta el comando, intente /alert [discord,server] [nick] [item] [price]"));
            return;
        }

        switch ($args[0]) {
            case "discord":
                if(empty($args[1])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el nick del jugador"));
                    return;
                }
                if(empty($args[2])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el artículo"));
                    return;
                }
                if(empty($args[3])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el precio del articulo"));
                    return;
                }
                $this->sendMessage(str_replace(["{player}", "{item}", "{price}"], [$args[1], $args[2], $args[3]], "**Jugador**: {player}\n\n**Artículo**: {item}\n\n**Precio**: {price}"));
                break;
            case "server":
                if(empty($args[1])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el nick del jugador"));
                    return;
                }
                if(empty($args[2])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el artículo"));
                    return;
                }
                if(empty($args[3])) {
                    $sender->sendMessage(TextFormat::colorize("&cTienes que poner el precio del articulo"));
                    return;
                }
                Main::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("\n &eEl jugador &c$args[1] &eha comprado &c$args[2] &epor &c$args[3] &een &2zodiac.tebex.io\n"));
                break;
        }
    }

    public function sendMessage(String $message): void {
        $embed = new Embed();
        $msg = new Message();
        $webhook = new Webhook("https://discord.com/api/webhooks/1024429192110157896/lZ0IxmVG1T5MW0HNgdXeX2y5-BWPW40_KzZbOuIlk0OzBIt4Vo4yY5OfsnZKGa9x2oKA");

        $msg->setUsername("Zodiac | Store");
        $embed->setTitle("Zodiac | Store");
        $embed->setDescription($message);
        $embed->setColor(0x478dff);
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }
}