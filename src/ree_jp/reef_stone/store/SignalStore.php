<?php

namespace ree_jp\reef_stone\store;

use pocketmine\world\Position;

class SignalStore extends JsonStore
{
    static SignalStore $instance;

    static function init(string $file): void
    {
        self::$instance = new self($file);
    }

    public function change(Position $pos, int $signal, string $key = "key"): void
    {
        if (!isset($this->data[$this->createKey($pos)])) $this->data[$this->createKey($pos)] = [];

        $this->data[$this->createKey($pos)][$key] = $signal;
        $this->saveData();
    }

    public function getMax(Position $pos): int
    {
        $maxPower = 0;
        if (isset($this->data[$this->createKey($pos)])) {
            foreach ($this->data[$this->createKey($pos)] as $power) {
                $maxPower = max($maxPower, $power);
            }
        }
        return $maxPower;
    }

    public function get(Position $pos, string $key): int
    {
        if (isset($this->data[$this->createKey($pos)]) && isset($this->data[$this->createKey($pos)][$key])) {
            return $this->data[$this->createKey($pos)][$key];
        }
        return 0;
    }

    public function remove(Position $pos): void
    {
        unset($this->data[$this->createKey($pos)]);
    }
}
