<?php
declare(strict_types=1);
#############################################
#             VERSION : 1.0.0               #
#############################################
#    Plugin Emojis Made By HighestDreams    #
#############################################
# You can send me your custom sticker via   #
# the link so that your sticker model plugin#
# can be included in the next updates!      #
# You can also help improve the plugin by   #
# reporting plug-in bugs                    #
# (only through GateHub!)!                  #
#############################################
namespace HighestDreams\Emojis;

use HighestDreams\Emojis\Form\Emojis;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as COLOR;

class Main extends PluginBase
{

    public const PREFIX = COLOR::BOLD . COLOR::YELLOW . "Emojis " . COLOR::RESET . COLOR::DARK_RED . "> ";
    public static $Instance;
    public static $config;
    public static $emojis = [];
    public static $timer = [];

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        return self::$Instance;
    }

    public function onEnable()
    {
        self::$Instance = $this;
        Entity::registerEntity(Emoji::class, true);

        foreach (['laugh', 'kiss', 'heart_kiss', 'heart_eyes', 'cool', 'cry'] as $geometries) {
            $this->saveResource("$geometries.json");
        }
        foreach (['emoji.png', 'config.yml'] as $otherResources) {
            $this->saveResource($otherResources);
        }

        self::$config = new Config($this->getDataFolder() . 'config.yml');

        $this->getScheduler()->scheduleRepeatingTask(new Timer($this), 20);
    }

    /**
     * @param CommandSender $player
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args): bool
    {
        if (!$player instanceof Player) return true;

        if (strtolower($cmd->getName()) === "emoji") {
            (new Emojis($this))->send($player);
        }
        return true;
    }

    /**
     * @param Player $player
     * @param string $emoji
     */
    public function spawn(Player $player, string $emoji)
    {
        if (isset(self::$emojis[$player->getName()])) {
            if (!is_null($entity = $player->getLevel()->getEntity(self::$emojis[$player->getName()]))) {
                $entity->flagForDespawn();
            }
        }

        $nbt = new CompoundTag("", [
            new ListTag("Pos", [
                new DoubleTag("", $player->getX()),
                new DoubleTag("", $player->getY()),
                new DoubleTag("", $player->getZ())
            ]),
            new ListTag("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            new ListTag("Rotation", [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ])
        ]);
        $nbt->setTag($player->namedtag->getTag("Skin"));
        $npc = (new Emoji($player->getLevel(), $nbt, $emoji));
        $npc->spawnToAll();
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($player->getId(), $npc->getId(), EntityLink::TYPE_RIDER, true, true);
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            $players->dataPacket($pk);
        }
        self::$emojis[$player->getName()] = $npc->getId();
        self::$timer[$player->getName()] = (self::$config->get('time') ?? 30);
    }
}