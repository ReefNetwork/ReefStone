<?php

namespace ree_jp\reef_stone\block;

use bbo51dog\bboform\element\Input;
use bbo51dog\bboform\form\ClosureCustomForm;
use bbo51dog\bboform\form\CustomForm;
use bbo51dog\bboform\form\SimpleForm;
use pocketmine\block\Opaque;
use pocketmine\block\utils\AnalogRedstoneSignalEmitterTrait;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use ree_jp\reef_stone\ReefStonePlugin;
use ree_jp\reef_stone\store\WirelessStore;
use tedo0627\redstonecircuit\block\BlockPowerHelper;
use tedo0627\redstonecircuit\block\BlockUpdateHelper;
use tedo0627\redstonecircuit\block\ILinkRedstoneWire;
use tedo0627\redstonecircuit\block\IRedstoneComponent;
use tedo0627\redstonecircuit\block\RedstoneComponentTrait;

class BlockRedstoneWirelessSend extends Opaque implements IRedstoneComponent, ILinkRedstoneWire
{
    public Vector3|null $target = null;

    use AnalogRedstoneSignalEmitterTrait;
    use RedstoneComponentTrait;

    public function onBreak(Item $item, ?Player $player = null): bool
    {
        $bool = parent::onBreak($item, $player);
        WirelessStore::$instance->remove($this);
        return $bool;
    }

    public function onRedstoneUpdate(): void
    {
        $power = 0;
        for ($face = 0; $face < 6; $face++) {
            $block = $this->getSide($face);
            $power = max($power, BlockPowerHelper::getWeakPower($block, $face));
        }
        if ($power === $this->getOutputSignalStrength()) return;
        $this->setOutputSignalStrength($power);
        $this->sendPower();
    }

    private function sendPower(): void
    {
        $this->target = WirelessStore::$instance->get($this);
        if ($this->target == null) return;
        $receive = $this->getPosition()->getWorld()->getBlock($this->target);
        if (!$receive instanceof BlockRedstoneWirelessReceive) return;
        $receive->setOutputSignalStrength($this->getOutputSignalStrength());
        BlockUpdateHelper::updateAroundRedstone($receive);
    }

    private function updateTarget(Vector3 $target): void
    {
        $this->target = $target;
        WirelessStore::$instance->save($this);
        $this->sendPower();
    }

    public function isConnect(int $face): bool
    {
        return true;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($player != null) {
            if (!ReefStonePlugin::coolTime($player->getXuid())) return true;
            $this->target = WirelessStore::$instance->get($this);
            $inputX = new Input("レッドストーン信号を受け取るブロックのX座標を入力してください", "数字", $this->target?->x);
            $inputY = new Input("レッドストーン信号を受け取るブロックのY座標を入力してください", "数字", $this->target?->y);
            $inputZ = new Input("レッドストーン信号を受け取るブロックのZ座標を入力してください", "数字", $this->target?->z);
            $form = (new ClosureCustomForm(function () use ($inputX, $inputY, $inputZ, $player): void {
                $this->updateTarget(new Vector3(intval($inputX->getValue()), intval($inputY->getValue()), intval($inputZ->getValue())));
                $player->sendMessage(intval($inputX->getValue()) . ":" . intval($inputY->getValue()) . ":" . intval($inputZ->getValue()) . "に座標が設定されました");
            }))->setTitle("Redstone Wireless Send");
            $form->addElements($inputX, $inputY, $inputZ);
            $player->sendForm($form);
            return true;
        }
        return false;
    }
}
