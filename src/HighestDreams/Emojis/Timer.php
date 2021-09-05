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

use pocketmine\scheduler\Task;
use pocketmine\Server;

class Timer extends Task {

    public $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        foreach (Main::$timer as $name => $timer) {
            if ($timer <= 0) {
                foreach(Server::getInstance()->getLevels() as $level) {
                    if (!is_null($entity = $level->getEntity(Main::$emojis[$name]))) {
                        $entity->flagForDespawn();
                    }
                }
                unset(Main::$emojis[$name]);
                unset(Main::$timer[$name]);
            } else {
                if (!is_null($player = Server::getInstance()->getPlayer($name))) {
                    foreach (Server::getInstance()->getLevels() as $level) {
                        if (!is_null($entity = $level->getEntity(Main::$emojis[$name]))) {
                            $entity->yaw = $player->getYaw();
                    }
                    }
                }
                Main::$timer[$name] = Main::$timer[$name] - 1;
            }
        }
    }
}