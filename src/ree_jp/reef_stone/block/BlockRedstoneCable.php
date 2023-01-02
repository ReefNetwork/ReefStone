<?php

namespace ree_jp\reef_stone\block;

use pocketmine\block\Opaque;
use pocketmine\block\RedstoneWire;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\utils\AnalogRedstoneSignalEmitterTrait;
use pocketmine\block\utils\SlabType;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use tedo0627\redstonecircuit\block\BlockPowerHelper;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;
use tedo0627\redstonecircuit\block\transmission\BlockRedstoneWire;
use tedo0627\redstonecircuit\event\BlockRedstoneSignalUpdateEvent;
use tedo0627\redstonecircuit\RedstoneCircuit;

class BlockRedstoneCable extends Opaque implements IRedstoneComponent, ILinkRedstoneWire
{
    use AnalogRedstoneSignalEmitterTrait;
    use RedstoneComponentTrait;

    public function onPostPlace(): void {
        $this->calculatePower();
    }

    // これをやらないとNormalBlockと判断されるので仕方なく
    public function isPowerSource(): bool
    {
        return true;
    }

    public function onBreak(Item $item, ?Player $player = null): bool {
        $bool = parent::onBreak($item, $player);
        BlockUpdateHelper::updateAroundRedstone($this);
        return $bool;
    }

    public function onNearbyBlockChange(): void {
        parent::onNearbyBlockChange();

        if ($this->getPosition()->getWorld()->getBlock($this->getPosition()) === VanillaBlocks::AIR()) return;
        if ($this->calculatePower()) return;
        BlockUpdateHelper::updateAroundRedstone($this);
    }

    public function getWeakPower(int $face): int {
        return $this->getOutputSignalStrength();
    }

    public function onRedstoneUpdate(): void {
        $this->calculatePower();
    }

    private function calculatePower(): bool {
        $power = 0;
        for ($face = 0; $face < 6; $face++) {
            $block = $this->getSide($face);
            if ($block instanceof BlockRedstoneCable) {
                $power = max($power, $block->getOutputSignalStrength() - 1);
                continue;
            }

            if (BlockPowerHelper::isPowerSource($block)) {
                $power = max($power, BlockPowerHelper::getWeakPower($block, $face));
            }
        }

        if ($this->getOutputSignalStrength() == $power) return false;

        if (RedstoneCircuit::isCallEvent()) {
            $event = new BlockRedstoneSignalUpdateEvent($this, $power, $this->getOutputSignalStrength());
            $event->call();

            $power = $event->getNewSignal();
            if ($this->getOutputSignalStrength() == $power) return false;
        }

        $this->setOutputSignalStrength($power);
        BlockUpdateHelper::updateAroundRedstone($this);
        return true;
    }

    public function isConnect(int $face): bool {
        return true;
    }
}
