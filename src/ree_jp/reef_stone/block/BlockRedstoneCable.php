<?php

namespace ree_jp\reef_stone\block;

use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\player\Player;
use ree_jp\reef_stone\CableConnection;
use ree_jp\reef_stone\CableSignalManager;
use tedo0627\redstonecircuit\block\BlockPowerHelper;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;
use tedo0627\redstonecircuit\block\transmission\BlockRedstoneWire;

class BlockRedstoneCable extends Opaque implements IRedstoneComponent, ILinkRedstoneWire
{
    use RedstoneComponentTrait;

    // これをやらないとNormalBlockと判断されるので仕方なく
    public function isPowerSource(): bool
    {
        return true;
    }

    public function onPostPlace(): void
    {
        $this->onRedstoneUpdate();
    }

    public function onBreak(Item $item, ?Player $player = null): bool
    {
        $bool = parent::onBreak($item, $player);
        // 一回ケーブルコネクションを壊して再生成する
        CableSignalManager::$instance->removeConnection($this->getPosition());
        BlockUpdateHelper::updateAroundRedstone($this);
        return $bool;
    }

    public function getWeakPower(int $face): int
    {
        $connection = CableSignalManager::$instance->getConnection($this->getPosition());
        if ($connection instanceof CableConnection) {
            return $connection->getMaxPower();
        } else {
            $this->onRedstoneUpdate();
            return $this->getWeakPower($face);
//            return 0;
        }
    }

    public function onRedstoneUpdate(): void
    {
        $power = 0;
        for ($face = 0; $face < 6; $face++) {
            $block = $this->getSide($face);
            if (!$block instanceof BlockRedstoneCable && !$block instanceof BlockRedstoneWire) {
                $power = max($power, BlockPowerHelper::getWeakPower($block, $face));
            }
        }

        $connection = CableSignalManager::$instance->getConnection($this->getPosition());
        if ($connection instanceof CableConnection) {
            $connection->updatePower($this->getPosition(), $power);
        } else {
            $this->joinConnection($power);
        }
    }

    private function joinConnection(int $power): void
    {
        $masterConnection = null;
        for ($face = 0; $face < 6; $face++) {
            $block = $this->getSide($face);
            if (!$block instanceof BlockRedstoneCable || ($block->idInfo->getBlockId() !== $this->idInfo->getBlockId())) continue;

            $connection = CableSignalManager::$instance->getConnection($block->getPosition());
            if (!$connection instanceof CableConnection) {
                continue;
            }

            if ($masterConnection === null) {
                $masterConnection = $connection;
                continue;
            }
            if ($masterConnection < $connection) {
                $oldMaster = $masterConnection;
                $masterConnection = $connection;
                $connection = $oldMaster;
            }

            if (!$masterConnection->equals($connection)) {
                $masterConnection->merge($connection);
            }
        }
        if ($masterConnection === null) { // 結局周囲にコネクションがなかったら作る
            $masterConnection = new CableConnection($this->getPosition()->getWorld());
        }
        CableSignalManager::$instance->joinConnections($masterConnection, $this->getPosition(), $power);
        BlockUpdateHelper::updateAroundRedstone($this);
    }

    public function isConnect(int $face): bool
    {
        return true;
    }
}
