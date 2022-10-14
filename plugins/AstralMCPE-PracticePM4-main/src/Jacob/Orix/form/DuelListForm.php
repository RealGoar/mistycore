<?php

namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use cosmicpe\form\types\Icon;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\duel\Queue;

class DuelListForm extends SimpleForm
{

	public function __construct(Player $player)
    {
        parent::__construct("§dSelect mode", "§7Select the duel mode you want to send");
        $modes = array_merge(AdvancedPractice::getInstance()->modes, AdvancedPractice::getInstance()->newModes);
        
        foreach ($modes as $mode => $data) {
            if ($mode === "Sumo") {
                continue;
            } else {
            $this->addButton(new Button($mode, new Icon(Icon::PATH, $data["icon"])), function (Player $target, int $index) use ($player, $mode) {
                if ($player === null) {
                    $target->sendMessage("§cThe player you are trying to invite is offline");
                    return;
                }
                $player->sendForm(new InviteForm($target, $player, $mode));
            });
        }
    }
}
}