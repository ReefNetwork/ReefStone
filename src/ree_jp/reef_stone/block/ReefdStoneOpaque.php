<?php

namespace ree_jp\reef_stone\block;

use pocketmine\block\Opaque;

abstract class ReefdStoneOpaque extends Opaque
{
    public function getFrictionFactor(): float
    {
        return 0.4;
    }
}
