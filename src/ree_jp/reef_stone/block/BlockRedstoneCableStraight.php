<?php

namespace ree_jp\reef_stone\block;

use customiesdevs\customies\block\permutations\Permutable;
use customiesdevs\customies\block\permutations\Permutation;
use customiesdevs\customies\block\permutations\RotatableTrait;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use tedo0627\redstonecircuit\block\BlockPowerHelper;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;

class BlockRedstoneCableStraight extends ReefdStoneOpaque implements IRedstoneComponent, ILinkRedstoneWire, Permutable
{
    use RotatableTrait;
    use RedstoneComponentTrait;

    public function isPowerSource(): bool
    {
        return true;
    }

    public function onPostPlace(): void
    {
        $this->onRedstoneUpdate();
    }

    public function onRedstoneUpdate(): void
    {
        $target = $this->getSide(Facing::opposite($this->facing));
        if ($target instanceof IRedstoneComponent) $target->onRedstoneUpdate();
    }

    public function onBreak(Item $item, ?Player $player = null): bool
    {
        $bool = parent::onBreak($item, $player);
        BlockUpdateHelper::updateAroundRedstone($this);
        return $bool;
    }

    public function getWeakPower(int $face): int
    {
        if ($face !== $this->facing) return 0;
        $source = $this->getSide($this->facing);
        return BlockPowerHelper::getWeakPower($source, $this->facing);
    }

    public function getPermutations(): array
    {
        return [
            (new Permutation("q.block_property('customies:rotation') == 2"))
                ->withComponent("minecraft:transformation", CompoundTag::create()
                    ->setInt("RX", 0)
                    ->setInt("RY", 0)
                    ->setInt("RZ", 0)
                    ->setFloat("SX", 1.0)
                    ->setFloat("SY", 1.0)
                    ->setFloat("SZ", 1.0)
                    ->setFloat("TX", 0.0)
                    ->setFloat("TY", 0.0)
                    ->setFloat("TZ", 0.0)),
            (new Permutation("q.block_property('customies:rotation') == 3"))
                ->withComponent("minecraft:transformation", CompoundTag::create()
                    ->setInt("RX", 0)
                    ->setInt("RY", 2)
                    ->setInt("RZ", 0)
                    ->setFloat("SX", 1.0)
                    ->setFloat("SY", 1.0)
                    ->setFloat("SZ", 1.0)
                    ->setFloat("TX", 0.0)
                    ->setFloat("TY", 0.0)
                    ->setFloat("TZ", 0.0)),
            (new Permutation("q.block_property('customies:rotation') == 4"))
                ->withComponent("minecraft:transformation", CompoundTag::create()
                    ->setInt("RX", 0)
                    ->setInt("RY", 1)
                    ->setInt("RZ", 0)
                    ->setFloat("SX", 1.0)
                    ->setFloat("SY", 1.0)
                    ->setFloat("SZ", 1.0)
                    ->setFloat("TX", 0.0)
                    ->setFloat("TY", 0.0)
                    ->setFloat("TZ", 0.0)),
            (new Permutation("q.block_property('customies:rotation') == 5"))
                ->withComponent("minecraft:transformation", CompoundTag::create()
                    ->setInt("RX", 0)
                    ->setInt("RY", 3)
                    ->setInt("RZ", 0)
                    ->setFloat("SX", 1.0)
                    ->setFloat("SY", 1.0)
                    ->setFloat("SZ", 1.0)
                    ->setFloat("TX", 0.0)
                    ->setFloat("TY", 0.0)
                    ->setFloat("TZ", 0.0)),
        ];
    }

    public function isConnect(int $face): bool
    {
        return true;
    }
}
