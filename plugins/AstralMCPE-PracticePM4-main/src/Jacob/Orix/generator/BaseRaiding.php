<?php namespace Jacob\Orix\generator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\data\bedrock\LegacyBlockIdToStringIdMap;
use pocketmine\item\Item;
use pocketmine\world\biome\Biome;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\BiomeArray;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use pocketmine\block\BlockLegacyIds;

class BaseRaiding extends Generator {

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        /** @phpstan-var Chunk $chunk */
        $chunk = $world->getChunk($chunkX, $chunkZ);

        if ($chunkX % 20 == 0 and $chunkZ % 20 == 0) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 0 or $z == 0) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 1 and $chunkZ % 20 == 0) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($z == 0) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 2 and $chunkZ % 20 == 0) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 15 or $z == 0) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 2 and $chunkZ % 20 == 1) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 15) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 2 and $chunkZ % 20 == 2) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 15 or $z == 15) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 0 and $chunkZ % 20 == 1) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 0) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 1 && $chunkZ % 20 == 1) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                    $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                    // Gates
                    $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                }
            }
        } else if ($chunkX % 20 == 1 && $chunkZ % 20 == 2) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($z == 15) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 0 && $chunkZ % 20 == 2) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($x == 0 or $z == 15) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        } else if ($chunkX % 20 == 1 && $chunkZ % 20 == 2) {
            for ($x = 0; $x < 16; $x++) {
                for ($z = 0; $z < 16; $z++) {
                    if ($z == 15 and $x == 15) {
                        for ($y = 99; $y < 256; $y++) {
                            $chunk->setFullBlock($x, $y, $z, VanillaBlocks::GLASS()->getFullId());
                        }
                    } else {
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::GRASS()->getFullId());
                        $chunk->setFullBlock($x, 100, $z, VanillaBlocks::DARK_OAK_PLANKS()->getFullId());
                        // Gates
                        $chunk->setFullBlock($x, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 101, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 102, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                        $chunk->setFullBlock($x + 1, 103, $z, VanillaBlocks::COBBLESTONE()->getFullId());
                    }
                }
            }
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
    }
}