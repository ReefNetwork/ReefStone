<?php

namespace ree_jp\reef_stone\block;

use pocketmine\block\Opaque;
use pocketmine\block\utils\AnalogRedstoneSignalEmitterTrait;
use pocketmine\item\Item;
use pocketmine\player\Player;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\LinkRedstoneWireTrait;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;

class BlockRedstoneWirelessReceive extends Opaque implements IRedstoneComponent
{
    use AnalogRedstoneSignalEmitterTrait;
    use LinkRedstoneWireTrait;
    use RedstoneComponentTrait;

    public function onPostPlace(): void {
        BlockUpdateHelper::updateAroundRedstone($this);
    }

    public function onBreak(Item $item, ?Player $player = null): bool {
        $bool = parent::onBreak($item, $player);
        BlockUpdateHelper::updateAroundRedstone($this);
        return $bool;
    }

    public function getWeakPower(int $face): int
    {
        return $this->getOutputSignalStrength();
    }

    public function isPowerSource(): bool
    {
        return true;
    }
}
