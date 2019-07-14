<?php

declare(strict_types=1);

namespace Frago9876543210\Scoreboards;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scoreboard\Scoreboard;
use function spl_object_id;

class Scoreboards extends PluginBase implements Listener{
	/** @var Scoreboard[][] */
	private static $scoreboards = [];

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public static function addScoreboard(Player $player, Scoreboard $scoreboard) : void{
		self::$scoreboards[spl_object_id($player)][spl_object_id($scoreboard)] = $scoreboard;
	}

	public static function removeScoreboard(Player $player, Scoreboard $scoreboard) : void{
		$playerId = spl_object_id($player);
		$scoreboardId = spl_object_id($scoreboard);
		if(isset(self::$scoreboards[$playerId][$scoreboardId])){
			unset(self::$scoreboards[$playerId][$scoreboardId]);
		}
	}

	/**
	 * @param PlayerQuitEvent $e
	 * @priority MONITOR
	 */
	public function onQuit(PlayerQuitEvent $e) : void{
		$player = $e->getPlayer();

		$id = spl_object_id($player);
		foreach(self::$scoreboards[$id] as $scoreboard){
			$scoreboard->removeViewer($player, false);
		}
		unset(self::$scoreboards[$id]);
	}
}
