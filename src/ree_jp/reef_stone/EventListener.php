<?php

namespace ree_jp\reef_stone;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Facing;
use tedo0627\redstonecircuit\block\IRedstoneComponent;

class EventListener implements Listener
{
    const color = [1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "l", "o"];

    public function onTouch(PlayerInteractEvent $ev): void
    {
        $p = $ev->getPlayer();
        $bl = $ev->getBlock();
        $color = "§" . self::color[mt_rand(0, 17)];
        $p->sendMessage("$color---------");
        if ($bl instanceof IRedstoneComponent) {
            $p->sendMessage("WEAK" . $bl->getWeakPower(Facing::UP) . " : STRONG" . $bl->getStrongPower(Facing::UP));
        }
        if (method_exists($bl, "getOutputSignalStrength")) {
            $p->sendMessage("output: ".$bl->getOutputSignalStrength());
        }
        $p->sendMessage("$color---------");
    }
}
