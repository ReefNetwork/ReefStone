<?php

namespace ree_jp\reef_stone\block;

use pocketmine\item\Item;
use pocketmine\player\Player;
use ree_jp\reef_stone\store\SignalStore;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\LinkRedstoneWireTrait;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;

class BlockRedstoneWirelessReceive extends ReefdStoneOpaque implements IRedstoneComponent
{
    use LinkRedstoneWireTrait;
    use RedstoneComponentTrait;

    public function setSignal(int $signalStrength): void
    {
        SignalStore::$instance->change($this->getPosition(), $signalStrength);
    }

    public function getSignal(): int
    {
        return SignalStore::$instance->get($this->getPosition());
    }

    public function onPostPlace(): void
    {
        BlockUpdateHelper::updateAroundRedstone($this);
    }

    public function onBreak(Item $item, ?Player $player = null): bool
    {
        $bool = parent::onBreak($item, $player);
        SignalStore::$instance->remove($this->getPosition());
        BlockUpdateHelper::updateAroundRedstone($this);
        return $bool;
    }

    public function getWeakPower(int $face): int
    {
        return $this->getSignal();
    }

    public function isPowerSource(): bool
    {
        return true;
    }
}
