<?php

namespace ree_jp\reef_stone\block;

use customiesdevs\customies\block\permutations\Permutable;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use ree_jp\reef_stone\Rotatable3DTrait;
use tedo0627\redstonecircuit\block\BlockPowerHelper;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;

class BlockRedstoneCableStraight extends ReefdStoneOpaque implements IRedstoneComponent, ILinkRedstoneWire, Permutable
{
    use Rotatable3DTrait;
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

    public function isConnect(int $face): bool
    {
        return true;
    }
}
