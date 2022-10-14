<?php namespace Jacob\Orix\entity;

use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\permission\DefaultPermissions;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\world\Position;
use Ramsey\Uuid\Uuid;

class UpdatableFloatingText
{
    /** @var int */
    private int $eid;
    /** @var string */
    private string $text;
    /** @var Position */

    private Position $position;
    private string $type = "";

    public function __construct(string $text, Position $position, int $eid, string $type)
    {
        $this->text = $text;
        $this->position = $position;
        $this->eid = Entity::nextRuntimeId();
        $this->type = $type;
    }

    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $text
     * @param Player $player
     */
    public function update(string $text, Player $player): void
    {
        $pk = new SetActorDataPacket();

        $pk->actorRuntimeId = Entity::nextRuntimeId();
        $pk->metadata = [EntityMetadataProperties::NAMETAG => new StringMetadataProperty($text)];
        $player->getNetworkSession()->sendDataPacket($pk);
    }
    /**
     * @param Player $player
     */
    public function remove(Player $player): void
    {
        $pk = new RemoveActorPacket();

        $pk->actorUniqueId  = Entity::nextRuntimeId();
        $player->getNetworkSession()->sendDataPacket($pk);
    }


}