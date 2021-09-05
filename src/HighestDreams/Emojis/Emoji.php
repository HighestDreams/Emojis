<?php

declare(strict_types=1);

namespace HighestDreams\Emojis;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Emoji extends Human {

    public static $emoji;

    public function __construct(Level $level, CompoundTag $nbt, string $emoji)
    {
        self::$emoji = $emoji;
        parent::__construct($level, $nbt);
    }

    /**
     * @return string
     */
    protected function getSkinBytes (): string
    {
        $path = Main::getInstance()->getDataFolder() . "emoji.png";
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];
        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $skinbytes;
    }

    public function initEntity(): void
    {
        $geoName = "geometry." . self::$emoji;
        $geoData = file_get_contents(Main::getInstance()->getDataFolder() . self::$emoji . ".json");
        $this->setSkin(new Skin($this->skin->getSkinId(), $this->getSkinBytes(), "", $geoName, $geoData));
        parent::initEntity();
    }

    /**
     * @return bool
     */
    public function canPickupXp(): bool
    {
        return false;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void{
        $source->setCancelled(true);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }
    public function hasMovementUpdate(): bool
    {
        return false;
    }
}