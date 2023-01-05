<?php

namespace ree_jp\reef_stone\store;

use JsonException;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use ree_jp\reef_stone\block\BlockRedstoneWirelessSend;
use RuntimeException;

class WirelessStore extends JsonStore
{
    static WirelessStore $instance;
    static function init(string $file): void
    {
        self::$instance = new self($file);
    }

    public function change(BlockRedstoneWirelessSend $bl): void
    {
        $value = ["x" => $bl->target->x, "y" => $bl->target->y, "z" => $bl->target->z];
        $this->data[$this->createKey($bl->getPosition())] = $value;
        $this->saveData();
    }

    public function get(BlockRedstoneWirelessSend $bl): ?Vector3
    {
        $value = $this->data[$this->createKey($bl->getPosition())];
        if ($value) {
            return new Vector3($value["x"], $value["y"], $value["z"]);
        }
        return null;
    }

    public function remove(BlockRedstoneWirelessSend $bl): void
    {
        unset($this->data[$this->createKey($bl->getPosition())]);
        $this->saveData();
    }
}
