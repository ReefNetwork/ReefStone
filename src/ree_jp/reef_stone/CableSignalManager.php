<?php

namespace ree_jp\reef_stone;

use pocketmine\world\Position;

class CableSignalManager
{
    static CableSignalManager $instance;

    /**
     * @var CableConnection[]
     */
    private array $connections;

    static function init(): void
    {
        self::$instance = new self();
    }

    // TODO もっといい方法

    public function joinConnections(CableConnection $connection, Position $pos, int $power): void
    {
        $this->connections[$this->createKey($pos)] = $connection;
        $connection->join($pos, $power);
    }

    protected function createKey(Position $pos): string
    {
        return $pos->getWorld()->getFolderName() . ":" . $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
    }

    public function removeConnection(Position $pos): void
    {
        $connection = $this->getConnection($pos);
        if ($connection == null) return;

        foreach ($connection->cables as $cable) {
            unset($this->connections[$this->createKey(Position::fromObject($cable->vec, $connection->world))]);
        }
    }

    public function getConnection(Position $pos): ?CableConnection
    {
        if (isset($this->connections[$this->createKey($pos)])) {
            return $this->connections[$this->createKey($pos)];
        }
        return null;
    }

    public function leaveConnection(Position $pos): void
    {
        unset($this->connections[$this->createKey($pos)]);
    }
}
