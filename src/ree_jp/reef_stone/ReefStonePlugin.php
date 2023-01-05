<?php

namespace ree_jp\reef_stone;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use ree_jp\reef_stone\block\BlockRedstoneCable;
use ree_jp\reef_stone\block\BlockRedstoneWirelessReceive;
use ree_jp\reef_stone\block\BlockRedstoneWirelessSend;
use ree_jp\reef_stone\store\SignalStore;
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
        SignalStore::init($this->getDataFolder() . "signal_store.json");
        CableSignalManager::init();

        $creativeInfo = new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE);

        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable White", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_white", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Orange", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_orange", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Magenta", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_magenta", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Light Blue", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_light_blue", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Yellow", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_yellow", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Lime", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_lime", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Pink", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_pink", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Gray", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_gray", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Light Gray", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_light_gray", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Cyan", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_cyan", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Purple", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_purple", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Blue", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_blue", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Brown", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_brown", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Green", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_green", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Red", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_red", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneCable(new BlockIdentifier($id, 0),
            "Redstone Cable Black", BlockBreakInfo::instant()), "reefd_stone:redstone_cable_black", null, $creativeInfo);

        WirelessStore::init($this->getDataFolder() . "wireless_block.json");
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneWirelessSend(new BlockIdentifier($id, 0),
            "Redstone Wireless Send", BlockBreakInfo::instant()), "reefd_stone:redstone_wireless_send", null, $creativeInfo);
        CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new BlockRedstoneWirelessReceive(new BlockIdentifier($id, 0),
            "Redstone Wireless Receive", BlockBreakInfo::instant()), "reefd_stone:redstone_wireless_receive", null, $creativeInfo);

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
        SignalStore::$instance->saveData();
    }
}
