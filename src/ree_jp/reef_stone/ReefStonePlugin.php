<?php

namespace ree_jp\reef_stone;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use ree_jp\reef_stone\block\BlockRedstoneCable;
use ree_jp\reef_stone\block\BlockRedstoneWirelessReceive;
use ree_jp\reef_stone\block\BlockRedstoneWirelessSend;
use ree_jp\reef_stone\store\WirelessStore;

class ReefStonePlugin extends PluginBase
{
    private static array $coolTime = [];
    public static ReefStonePlugin $plugin;

    public function onEnable(): void
    {
        self::$plugin = $this;
        $this->init();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    private function init(): void
    {
        $creativeInfo = new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable", BlockBreakInfo::instant()), "reef_stone:redstone_cable", null, $creativeInfo);
        WirelessStore::init($this->getDataFolder());
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneWirelessSend(new BlockIdentifier($id, 0),
            "Redstone Wireless Send", BlockBreakInfo::instant()), "reef_stone:redstone_wireless_send", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneWirelessReceive(new BlockIdentifier($id, 0),
            "Redstone Wireless Receive", BlockBreakInfo::instant()), "reef_stone:redstone_wireless_receive", null, $creativeInfo);
    }

    static function coolTime(string $xuid): bool
    {
        if (isset(self::$coolTime[$xuid])) {
            return false;
        } else {
            self::$coolTime[$xuid] = "a";
            self::$plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($xuid): void {
                unset(self::$coolTime[$xuid]);
            }), 3);
            return true;
        }
    }

    public function onDisable(): void
    {
        parent::onDisable();
    }
}
