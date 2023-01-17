<?php

namespace ree_jp\reef_stone;

use customiesdevs\customies\block\CustomiesBlockFactory;
use customiesdevs\customies\block\Material;
use customiesdevs\customies\block\Model;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use ree_jp\reef_stone\block\BlockRedstoneCable;
use ree_jp\reef_stone\block\BlockRedstoneCableBind;
use ree_jp\reef_stone\block\BlockRedstoneCableStraight;
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

        $origin = new Vector3(-8, 0, -8);
        $vec16 = new Vector3(16, 16, 16);

        foreach ([
                     new CustomiesBlock("白色のレッドストーンケーブル", "reefd_stone:redstone_cable_white", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_white")),
                     new CustomiesBlock("オレンジ色のレッドストーンケーブル", "reefd_stone:redstone_cable_orange", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_orange")),
                     new CustomiesBlock("青紫色のレッドストーンケーブル", "reefd_stone:redstone_cable_magenta", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_magenta")),
                     new CustomiesBlock("空色のレッドストーンケーブル", "reefd_stone:redstone_cable_light_blue", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_light_blue")),
                     new CustomiesBlock("黄色のレッドストーンケーブル", "reefd_stone:redstone_cable_yellow", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_yellow")),
                     new CustomiesBlock("黄緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_lime", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_lime")),
                     new CustomiesBlock("ピンクのレッドストーンケーブル", "reefd_stone:redstone_cable_pink", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_pink")),
                     new CustomiesBlock("灰色のレッドストーンケーブル", "reefd_stone:redstone_cable_gray", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_gray")),
                     new CustomiesBlock("薄灰色のレッドストーンケーブル", "reefd_stone:redstone_cable_light_gray", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_light_gray")),
                     new CustomiesBlock("青緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_cyan", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_cyan")),
                     new CustomiesBlock("紫色のレッドストーンケーブル", "reefd_stone:redstone_cable_purple", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_purple")),
                     new CustomiesBlock("青色のレッドストーンケーブル", "reefd_stone:redstone_cable_blue", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_blue")),
                     new CustomiesBlock("茶色のレッドストーンケーブル", "reefd_stone:redstone_cable_brown", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_brown")),
                     new CustomiesBlock("緑色のレッドストーンケーブル", "reefd_stone:redstone_cable_green", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_green")),
                     new CustomiesBlock("赤色のレッドストーンケーブル", "reefd_stone:redstone_cable_red", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_red")),
                     new CustomiesBlock("黒色のレッドストーンケーブル", "reefd_stone:redstone_cable_black", BlockRedstoneCable::class,
                         $this->normalModel("redstone_cable_black")),

                     new CustomiesBlock("レッドストーン無線送信機", "reefd_stone:redstone_wireless_send", BlockRedstoneWirelessSend::class,
                         $this->normalModel("redstone_wireless_send")),
                     new CustomiesBlock("レッドストーン無線受信機", "reefd_stone:redstone_wireless_receive", BlockRedstoneWirelessReceive::class,
                         $this->normalModel("redstone_wireless_receive")),

                     new CustomiesBlock("レッドストーンケーブル結合機", "reefd_stone:redstone_cable_bind", BlockRedstoneCableBind::class,
                         $this->normalModel("redstone_cable_bind")),

                     new CustomiesBlock("白色の直進レッドストーンケーブル", "reefd_stone:redstone_cable_straight_white", BlockRedstoneCableStraight::class,
                         new Model([new Material(Material::TARGET_ALL, "redstone_cable_straight_white", Material::RENDER_METHOD_ALPHA_TEST),
                             new Material(Material::TARGET_NORTH, "redstone_cable_white", Material::RENDER_METHOD_ALPHA_TEST),
                             new Material(Material::TARGET_SOUTH, "redstone_cable_white", Material::RENDER_METHOD_ALPHA_TEST),
                             new Material(Material::TARGET_EAST, "redstone_cable_straight_white_east", Material::RENDER_METHOD_ALPHA_TEST),
                             new Material(Material::TARGET_WEST, "redstone_cable_straight_white_west", Material::RENDER_METHOD_ALPHA_TEST)],
                             "geometry.block", $origin, $vec16)),
                 ] as $customies) {
            $class = $customies->class;
            $name = $customies->name;
            CustomiesBlockFactory::getInstance()->registerBlock(fn(int $id) => new $class(new BlockIdentifier($id, 0),
                $name, BlockBreakInfo::instant()), $customies->identifier, $customies->model, $creativeInfo);
        }
    }

    private function normalModel(string $texture): Model
    {
        return new Model([new Material(Material::TARGET_ALL, $texture, Material::RENDER_METHOD_ALPHA_TEST)],
            "geometry.block", new Vector3(-8, 0, -8), new Vector3(16, 16, 16));
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
    public function __construct(public string $name, public string $identifier, public string $class, public ?Model $model = null)
    {
    }
}
