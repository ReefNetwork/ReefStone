<?php

namespace ree_jp\reef_stone\event;

use pocketmine\event\block\BlockEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use ree_jp\reef_stone\block\BlockRedstoneWirelessReceive;
use ree_jp\reef_stone\block\BlockRedstoneWirelessSend;

class BlockRedstoneWirelessSignalUpdate extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(BlockRedstoneWirelessSend $block, private BlockRedstoneWirelessReceive $target, private int $newSignal, private int $oldSignal)
    {
        parent::__construct($block);
    }

    public function getTarget(): BlockRedstoneWirelessReceive
    {
        return $this->target;
    }

    public function getNewSignal(): int
    {
        return $this->newSignal;
    }

    public function setNewSignal(int $signal): void
    {
        $this->newSignal = $signal;
    }

    public function getOldSignal(): int
    {
        return $this->oldSignal;
    }
}
