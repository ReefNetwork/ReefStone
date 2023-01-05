<?php

namespace ree_jp\reef_stone;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;
use ree_jp\reef_stone\block\BlockRedstoneCable;
use tedo0627\redstonecircuit\block\IRedstoneComponent;

class CableConnection
{
    /**
     * @var Cable[]
     */
    public array $cables = [];
    public World $world;
    private int $maxPower = 0;

    public function __construct(World $world)
    {
        $this->world = $world;
    }

    public function getMaxPower(): int
    {
        return $this->maxPower;
    }

    public function updatePower(Vector3 $vec, int $power): void
    {
        foreach ($this->cables as $cable) {
            if ($cable->vec->equals($vec)) {
                if ($cable->power === $power) return;

                $oldPower = $cable->power;
                $cable->power = $power;

                if ($this->maxPower < $power) { // コネクションで信号が一番大きくなった場合
                    $this->maxPower = $power;
                    $this->updateAllCableSignal();
                } else if ($this->maxPower === $oldPower) { // 元々信号が一番大きくて下がった場合
                    $newMax = $this->checkMaxPower();
                    if ($this->maxPower > $newMax) {
                        $this->maxPower = $newMax;
                        $this->updateAllCableSignal();
                    }
                }
                return;
            }
        }
    }

    //ケーブルの信号を周りに反映させる

    public function equals(CableConnection $connection): bool
    {
        if ($connection->maxPower !== $this->maxPower) return false;
        foreach ($connection->cables as $key => $cable) {
            if (!isset($this->cables[$key])) return false;

            // 1つのケーブルだけでも同じのがあったら全部同じと判断
            return $cable->equals($this->cables[$key]);
        }
        return true;
    }

    private function updateAllCableSignal(): void
    {
        foreach ($this->cables as $cable) {
            $center = $this->world->getBlock($cable->vec);
            $this->updateCableSignal($center);
        }
    }

    private function updateCableSignal(Block $cable): void
    {
        for ($face = 0; $face < 6; $face++) {
            $block = $cable->getSide($face);
            if (!$block instanceof BlockRedstoneCable && $block instanceof IRedstoneComponent) $block->onRedstoneUpdate();
        }
    }

    private function checkMaxPower(): int
    {
        $power = 0;
        foreach ($this->cables as $cable) {
            $power = max($power, $cable->power);
        }
        return $power;
    }

    public function join(Vector3 $vec, int $power): void
    {
        foreach ($this->cables as $cable) {
            if ($cable->vec->equals($vec)) return;
        }
        $new = new Cable($vec, $power);
        $this->cables[] = $new;
        if ($this->maxPower >= $power) return;
        $this->maxPower = $power;
        $this->updateAllCableSignal();
    }

    public function merge(CableConnection $connection): void
    {
        foreach ($connection->cables as $cable) {
            $pos = Position::fromObject($cable->vec, $this->world);
            CableSignalManager::$instance->leaveConnection($pos);
            CableSignalManager::$instance->joinConnections($this, $pos, $cable->power);
            $this->updateCableSignal($this->world->getBlock($pos));
        }
    }
}

class Cable
{
    public function __construct(public Vector3 $vec, public int $power)
    {
    }

    public function equals(Cable $cable): bool
    {
        return ($cable->power === $this->power) && $cable->vec->equals($this->vec);
    }
}
