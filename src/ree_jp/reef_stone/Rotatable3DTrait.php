<?php

namespace ree_jp\reef_stone;

use customiesdevs\customies\block\permutations\BlockProperty;
use customiesdevs\customies\block\permutations\Permutation;
use customiesdevs\customies\block\permutations\Permutations;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

trait Rotatable3DTrait
{
    protected int $facing;

    /**
     * @return BlockProperty[]
     */
    public function getBlockProperties(): array
    {
        return [
            new BlockProperty("customies:rotation", [0, 1, 2, 3, 4, 5]),
        ];
    }

    /**
     * @return Permutation[]
     */
    public function getPermutations(): array
    {
        return [
            (new Permutation("q.block_property('customies:rotation') == 0"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", -90)
                    ->setFloat("y", 0)
                    ->setFloat("z", 0)),
            (new Permutation("q.block_property('customies:rotation') == 1"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 90)
                    ->setFloat("y", 0)
                    ->setFloat("z", 0)),
            (new Permutation("q.block_property('customies:rotation') == 2"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 0)
                    ->setFloat("y", 0)
                    ->setFloat("z", 0)),
            (new Permutation("q.block_property('customies:rotation') == 3"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 0)
                    ->setFloat("y", 180)
                    ->setFloat("z", 0)),
            (new Permutation("q.block_property('customies:rotation') == 4"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 0)
                    ->setFloat("y", 90)
                    ->setFloat("z", 0)),
            (new Permutation("q.block_property('customies:rotation') == 5"))
                ->withComponent("minecraft:rotation", CompoundTag::create()
                    ->setFloat("x", 0)
                    ->setFloat("y", 270)
                    ->setFloat("z", 0)),
        ];
    }

    public function getCurrentBlockProperties(): array
    {
        return [$this->facing];
    }

    public function readStateFromData(int $id, int $stateMeta): void
    {
        $blockProperties = Permutations::fromMeta($this, $stateMeta);
        $this->facing = $blockProperties[0] ?? Facing::UP;
    }

    public function getStateBitmask(): int
    {
        return Permutations::getStateBitmask($this);
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($player !== null) {
            $this->facing = $this->getFacing($player);
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    protected function getFacing(Player $p): int
    {
        $pitch = $p->getLocation()->pitch;
        if ($pitch < -45) {
            return Facing::UP;
        }
        if ($pitch > 45) {
            return Facing::DOWN;
        }
        return $p->getHorizontalFacing();
    }

    protected function writeStateToMeta(): int
    {
        return Permutations::toMeta($this);
    }
}
