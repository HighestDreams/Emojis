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
namespace HighestDreams\Emojis\Form;

use HighestDreams\Emojis\Form\formapi\FormAPI;
use HighestDreams\Emojis\Main;
use pocketmine\Player;
use pocketmine\utils\TextFormat as COLOR;

class Emojis
{

    private $main;

    public function __construct(Main $main)
    {
        $this->main = $main;
    }

    /**
     * @param Player $player
     */
    public function send(Player $player)
    {
        $emojis = ['laugh', 'kiss', 'heart_eyes', 'cool', 'cry', 'heart_kiss'];
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data = null) use ($emojis) {
            if (is_null($data)) return;

            foreach ($emojis as $result => $emoji) {
                if ($data === $result) {
                    $this->main->spawn($player, $emoji);
                    $player->sendMessage(Main::PREFIX . COLOR::GREEN . Main::$config->get('message') . ".");
                    break;
                }
            }
        });
        $form->setTitle("§l§eE§6M§eO§6J§eI§6S §bMenu");
        $form->setContent("§3Choose an emoji.");
        foreach ($emojis as $emoji) {
            $form->addButton("§l§2$emoji");
        }
        $form->sendToPlayer($player);
    }
}