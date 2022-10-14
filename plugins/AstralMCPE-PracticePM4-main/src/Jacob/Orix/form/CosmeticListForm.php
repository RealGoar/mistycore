<?php namespace Jacob\Orix\form;

use cosmicpe\form\entries\simple\Button;
use cosmicpe\form\SimpleForm;
use pocketmine\player\Player;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\cosmetic\cosmeticType\CosmeticType;
use Jacob\Orix\cosmetic\cosmeticType\WalkingParticleCosmetic;

class CosmeticListForm extends SimpleForm {

	public function __construct(string $type, Player $player) {
		parent::__construct($type);
		$list = AdvancedPractice::getCosmetics()->cosmetics[$type];
		foreach ($list as $class) {
			if ($class instanceof CosmeticType) {
				$canEquip = false;
				$equipType = $class->isPermissionNeeded();
				if ($equipType) {
					if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->hasPermission($class->getPermission())) $canEquip = true;
				} else if (AdvancedPractice::getSessionManager()->getPlayerSession($player)->getKills() >= $class->getKillsNeeded()) $canEquip = true;
				$this->addButton(new Button($class->getName()."\n".($canEquip ? "§l§aAVAILABLE!": ($equipType ? "§rMissing Permissions" : "Need ".($class->getKillsNeeded() - AdvancedPractice::getSessionManager()->getPlayerSession($player)->getKills())." More Kill(s)"))), function(Player $player, int $index) use ($class, $canEquip) {
					if (!$canEquip) {
						$player->sendMessage("§dYou do not have permission to equip this cosmetic.");
						return;
					}
					if ($class instanceof WalkingParticleCosmetic) {
						AdvancedPractice::getSessionManager()->getPlayerSession($player)->setWalkingParticle($class);
						$player->sendMessage("§dYou have equipped the §5".$class->getName()." §dWalking Particle!");
					}
				});
			}
		}
	}

}