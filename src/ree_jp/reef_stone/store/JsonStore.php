<?php

namespace ree_jp\reef_stone\store;

use JsonException;
use pocketmine\utils\Config;
use pocketmine\world\Position;

abstract class JsonStore
{
    protected Config $config;

    protected array $data;

    public function __construct(string $file)
    {
        $this->config = new Config($file, Config::JSON);
        $this->data = $this->config->getAll();
    }

    public function saveData(): bool
    {
        try {
            $this->config->setAll($this->data);
            $this->config->save();
        } catch (JsonException $e) {
            var_dump($e);
            return false;
        }
        return true;
    }

    protected function createKey(Position $pos): string
    {
        return $pos->getWorld()->getFolderName() . ":" . $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
    }
}
