<?php

namespace ree_jp\reef_stone\store;

use pocketmine\math\Vector3;
use pocketmine\world\Position;
use RuntimeException;

class SignalStore extends JsonStore
{
    static SignalStore $instance;
    static function init(string $file): void
    {
        self::$instance = new self($file);
    }

    public function change(Position $pos, int $signal): void
    {
        $this->data[$this->createKey($pos)] = $signal;
        $this->saveData();
    }

    public function get(Position $pos): int
    {
        $value = $this->data[$this->createKey($pos)];
        if ($value) {
            return $value;
        }
        return 0;
    }

    public function remove(Position $pos): void
    {
        unset($this->data[$this->createKey($pos)]);
    }
}
