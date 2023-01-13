<?php

namespace ree_jp\reef_stone;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use ree_jp\reef_stone\block\BlockRedstoneCable;
use ree_jp\reef_stone\block\BlockRedstoneCableBind;
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
        CableSignalManager::init();
        SignalStore::init($this->getDataFolder() . "signal_store.json");
        WirelessStore::init($this->getDataFolder() . "wireless_block.json");

        $creativeInfo = new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::NONE);

        foreach ([
                     new CustomiesBlock("白色のレッドストーンケーブル", "reefd_stone:redstone_cable_white", BlockRedstoneCable::class),
                     new CustomiesBlock("オレンジ色のレッドストーンケーブル", "reefd_stone:redstone_cable_orange", BlockRedstoneCable::class),
                     new CustomiesBlock("青紫色のレッドストーンケーブル", "reefd_stone:redstone_cable_magenta", BlockRedstoneCable::class),
                     new CustomiesBlock("空色のレッドストーンケーブル", "reefd_stone:redstone_cable_light_blue", BlockRedstoneCable::class),
                     new CustomiesBlock("黄色のレッドストーンケーブル", "reefd_stone:redstone_cable_yellow", BlockRedstoneCable::class),
                     new CustomiesBlock("黄緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_lime", BlockRedstoneCable::class),
                     new CustomiesBlock("ピンクのレッドストーンケーブル", "reefd_stone:redstone_cable_pink", BlockRedstoneCable::class),
                     new CustomiesBlock("灰色のレッドストーンケーブル", "reefd_stone:redstone_cable_gray", BlockRedstoneCable::class),
                     new CustomiesBlock("薄灰色のレッドストーンケーブル", "reefd_stone:redstone_cable_light_gray", BlockRedstoneCable::class),
                     new CustomiesBlock("青緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_cyan", BlockRedstoneCable::class),
                     new CustomiesBlock("紫色のレッドストーンケーブル", "reefd_stone:redstone_cable_purple", BlockRedstoneCable::class),
                     new CustomiesBlock("青色のレッドストーンケーブル", "reefd_stone:redstone_cable_blue", BlockRedstoneCable::class),
                     new CustomiesBlock("茶色のレッドストーンケーブル", "reefd_stone:redstone_cable_brown", BlockRedstoneCable::class),
                     new CustomiesBlock("緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_green", BlockRedstoneCable::class),
                     new CustomiesBlock("赤色のレッドストーンケーブル", "reefd_stone:redstone_cable_red", BlockRedstoneCable::class),
                     new CustomiesBlock("黒色のレッドストーンケーブル", "reefd_stone:redstone_cable_black", BlockRedstoneCable::class),

                     new CustomiesBlock("レッドストーン無線送信機", "reefd_stone:redstone_wireless_send", BlockRedstoneWirelessSend::class),
                     new CustomiesBlock("レッドストーン無線受信機", "reefd_stone:redstone_wireless_receive", BlockRedstoneWirelessReceive::class),

                     new CustomiesBlock("レッドストーンケーブル結合機", "reefd_stone:redstone_cable_bind", BlockRedstoneCableBind::class),
                 ] as $customies) {
            $class = $customies->class;
            $name = $customies->name;
            CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new $class(new BlockIdentifier($id, 0),
                $name, BlockBreakInfo::instant()), $customies->identifier, null, $creativeInfo);
        }
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

class CustomiesBlock
{
    public function __construct(public string $name, public string $identifier, public string $class)
    {
    }
}
