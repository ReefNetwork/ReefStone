<?php

namespace ree_jp\reef_stone\store;

use JsonException;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use ree_jp\reef_stone\block\BlockRedstoneWirelessSend;

class WirelessStore
{
    static WirelessStore $instance;
    private Config $config;
    private array $data;

    static function init(string $folder): void
    {
        self::$instance = new WirelessStore($folder);
    }

    public function __construct(string $folder)
    {
        $this->config = new Config($folder . "wireless_block.json", Config::JSON);
        $this->data = $this->config->getAll();
    }

    public function save(BlockRedstoneWirelessSend $bl): void
    {
        $value = ["x" => $bl->target->x, "y" => $bl->target->y, "z" => $bl->target->z];
        $this->data[$this->createKey($bl->getPosition())] = $value;
        try {
            $this->config->setAll($this->data);
            $this->config->save();
        } catch (JsonException $e) {
            var_dump($e);
        }
    }

    public function get(BlockRedstoneWirelessSend $bl): ?Vector3
    {
        $value = $this->config->get($this->createKey($bl->getPosition()));
        if ($value) {
            return new Vector3($value["x"], $value["y"], $value["z"]);
        }
        return null;
    }

    public function remove(BlockRedstoneWirelessSend $bl): void
    {
        unset($this->data[$this->createKey($bl->getPosition())]);
        try {
            $this->config->setAll($this->data);
            $this->config->save();
        } catch (JsonException $e) {
            var_dump($e);
        }
    }

    private function createKey(Position $pos): string
    {
        return $pos->getWorld()->getFolderName() . ":" . $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
    }
}
