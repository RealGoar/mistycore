<?php

declare(strict_types=1);

namespace Jacob\Orix\task;

use pocketmine\player\Player;
use Jacob\Orix\constants\ParticleConstants;
use Jacob\Orix\AdvancedPractice;
use Jacob\Orix\particle\SpiralParticle;
use Jacob\Orix\particle\TrailParticle;
use Jacob\Orix\particle\WingParticle;
use pocketmine\color\Color;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\EnchantmentTableParticle;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\HappyVillagerParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\LavaDripParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\WaterDripParticle;
use pocketmine\world\particle\WaterParticle;

class ParticleDisplayTask extends Task
{
    protected AdvancedPractice $plugin;

    public function __construct(AdvancedPractice $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        $session = AdvancedPractice::getSessionManager();
        foreach (AdvancedPractice::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($session->getPlayerSession($player)->getWing() !== null) {
                switch ($session->getPlayerSession($player)->getWing()) {
                    case ParticleConstants::WING_PARTICLES["BLACK"]:
                        WingParticle::display($player, new DustParticle(new Color(0, 0, 0)), new DustParticle(new Color(37, 41, 38)));
                        break;
                    case ParticleConstants::WING_PARTICLES["BROWN"]:
                        WingParticle::display($player, new DustParticle(new Color(105, 45, 19)), new DustParticle(new Color(145, 68, 35)));
                        break;
                    case ParticleConstants::WING_PARTICLES["BLUE"]:
                        WingParticle::display($player, new DustParticle(new Color(0, 106, 255)), new DustParticle(new Color(0, 174, 255)));
                        break;
                    case ParticleConstants::WING_PARTICLES["GREEN"]:
                        WingParticle::display($player, new DustParticle(new Color(15, 209, 47)), new DustParticle(new Color(33, 255, 70)));
                        break;
                    case ParticleConstants::WING_PARTICLES["GRAY"]:
                        WingParticle::display($player, new DustParticle(new Color(96, 105, 98)), new DustParticle(new Color(148, 146, 146)));
                        break;
                    case ParticleConstants::WING_PARTICLES["ORANGE"]:
                        WingParticle::display($player, new DustParticle(new Color(255, 145, 0)), new DustParticle(new Color(252, 165, 50)));
                        break;
                    case ParticleConstants::WING_PARTICLES["PINK"]:
                        WingParticle::display($player, new DustParticle(new Color(224, 2, 210)), new DustParticle(new Color(255, 82, 244)));
                        break;
                    case ParticleConstants::WING_PARTICLES["PURPLE"]:
                        WingParticle::display($player, new DustParticle(new Color(140, 0, 255)), new DustParticle(new Color(168, 82, 255)));
                        break;
                    case ParticleConstants::WING_PARTICLES["LIGHT_PURPLE"]:
                        WingParticle::display($player, new DustParticle(new Color(255, 0, 0)), new DustParticle(new Color(255, 61, 61)));
                        break;
                    case ParticleConstants::WING_PARTICLES["WHITE"]:
                        WingParticle::display($player, new DustParticle(new Color(186, 186, 186)), new DustParticle(new Color(237, 232, 232)));
                        break;
                    case ParticleConstants::WING_PARTICLES["YELLOW"]:
                        WingParticle::display($player, new DustParticle(new Color(255, 230, 0)), new DustParticle(new Color(255, 237, 69)));
                }
            }
            if ($session->getPlayerSession($player)->getParticle() !== null) {
                switch ($session->getPlayerSession($player)->getParticle()) {
                    case ParticleConstants::SPIRAL_PARTICLES["ANGRY VILLAGER"]:
                        SpiralParticle::display($player, new AngryVillagerParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["HAPPY VILLAGER"]:
                        SpiralParticle::display($player, new HappyVillagerParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["HEARTS"]:
                        SpiralParticle::display($player, new HeartParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["WATER"]:
                        SpiralParticle::display($player, new WaterParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["WATER DRIP"]:
                        SpiralParticle::display($player, new WaterDripParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["LAVA"]:
                        SpiralParticle::display($player, new LavaParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["LAVA DRIP"]:
                        SpiralParticle::display($player, new LavaDripParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["FLAME"]:
                        SpiralParticle::display($player, new FlameParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["PORTAL"]:
                        SpiralParticle::display($player, new PortalParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["REDSTONE"]:
                        SpiralParticle::display($player, new RedstoneParticle());
                        break;
                    case ParticleConstants::SPIRAL_PARTICLES["ENCHANTMENT"]:
                        SpiralParticle::display($player, new EnchantmentTableParticle());
                }
            }
            if ($session->getPlayerSession($player)->getTrail() !== null) {
                switch ($session->getPlayerSession($player)->getTrail()) {
                    case ParticleConstants::TRAIL_PARTICLES["ANGRY VILLAGER"]:
                        TrailParticle::display($player, new AngryVillagerParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["HAPPY VILLAGER"]:
                        TrailParticle::display($player, new HappyVillagerParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["HEARTS"]:
                        TrailParticle::display($player, new HeartParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["WATER"]:
                        TrailParticle::display($player, new WaterParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["WATER DRIP"]:
                        TrailParticle::display($player, new WaterDripParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["LAVA"]:
                        TrailParticle::display($player, new LavaParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["LAVA DRIP"]:
                        TrailParticle::display($player, new LavaDripParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["FLAME"]:
                        TrailParticle::display($player, new FlameParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["PORTAL"]:
                        TrailParticle::display($player, new PortalParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["REDSTONE"]:
                        TrailParticle::display($player, new RedstoneParticle());
                        break;
                    case ParticleConstants::TRAIL_PARTICLES["ENCHANTMENT"]:
                        TrailParticle::display($player, new EnchantmentTableParticle());
                }
            }
        }
    }
}
