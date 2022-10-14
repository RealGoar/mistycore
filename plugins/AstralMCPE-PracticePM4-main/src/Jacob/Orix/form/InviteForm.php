<?php


namespace Jacob\Orix\form;


use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use Jacob\Orix\duel\Duel;
use pocketmine\player\Player;

class InviteForm extends SimpleForm
{

    public function __construct($inviter, $receiver, $mode)
    {
        if ($inviter instanceof Player) {
            $name = $inviter->getName();
            parent::__construct("§4Duel Invite", "§5$name has invited you to play $mode.");
            $this->addButton(new Button("§a§lAccept"), function (Player $player, int $index) use ($inviter, $mode, $receiver) {
                new Duel(false, "$mode", $inviter, $player); #Jacoob dont touch this
            });
            $this->addButton(new Button("§c§lDeny"), function (Player $player, int $index) use ($inviter, $mode, $receiver) {
                if ($inviter instanceof Player) {
                    $receivern = $receiver->getName();
                    $invitern = $inviter->getName();
                    $inviter->sendMessage("§l§7[§4!§7] - §r§7$receivern has declined your invite.");
                    $player->sendMessage("§l§7[§4!§7] - §r§7You have declined the invite of $invitern.");
                }
            });
        }
    }
}    
