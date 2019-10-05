<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\scoreboard;

use Frago9876543210\Scoreboards\protocol\RemoveObjectivePacket;
use Frago9876543210\Scoreboards\protocol\SetDisplayObjectivePacket;
use Frago9876543210\Scoreboards\protocol\SetScorePacket;
use Frago9876543210\Scoreboards\protocol\types\ScorePacketEntry;
use Frago9876543210\Scoreboards\Scoreboards;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry as _ScorePacketEntry;
use pocketmine\player\Player;
use function spl_object_id;

class Scoreboard{
	/** @var Objective */
	private $objective;
	/** @var int */
	private $entryUniqueId = 0;
	/** @var _ScorePacketEntry[] */
	private $entries = [];
	/** @var Player[] */
	private $viewers = [];

	public function __construct(Objective $objective){
		$this->objective = $objective;
	}

	/**
	 * @return Objective
	 */
	public function getObjective() : Objective{
		return $this->objective;
	}

	/**
	 * @return _ScorePacketEntry[]
	 */
	public function getEntries() : array{
		return $this->entries;
	}

	/**
	 * @param string $player
	 * @return int|null
	 */
	public function getScore(string $player) : ?int{
		return $this->entries[$player]->score ?? null;
	}

	/**
	 * @param string $player
	 * @param int    $score
	 */
	public function setScore(string $player, int $score) : void{
		$entry = $this->entries[$player] ?? ScorePacketEntry::create($this->entryUniqueId++, $this->objective->objectiveName, $score, $player);
		$entry->score = $score;
		$this->entries[$player] = $entry;

		$pk = SetScorePacket::change([$entry]);

		foreach($this->viewers as $viewer){
			$viewer->getNetworkSession()->sendDataPacket($pk);
		}
	}

	/**
	 * @param string $player
	 */
	public function resetScore(string $player) : void{
		if(isset($this->entries[$player])){
			$entry = $this->entries[$player];
			$pk = SetScorePacket::remove([$entry]);
			foreach($this->viewers as $viewer){
				$viewer->getNetworkSession()->sendDataPacket($pk);
			}
			unset($this->entries[$player]);
		}
	}

	/**
	 * @return Player[]
	 */
	public function getViewers() : array{
		return $this->viewers;
	}

	/**
	 * @param Player $player
	 */
	public function addViewer(Player $player) : void{
		Scoreboards::addScoreboard($player, $this);

		$objective = $this->getObjective();
		$player->getNetworkSession()->sendDataPacket(SetDisplayObjectivePacket::create(
			$objective->displaySlot->name(),
			$objective->objectiveName,
			$objective->displayName,
			$objective->criteriaName,
			$objective->sortOrder->getMagicNumber()
		));
		$player->getNetworkSession()->sendDataPacket(SetScorePacket::change($this->entries));

		$this->viewers[spl_object_id($player)] = $player;
	}

	/**
	 * @param Player $player
	 * @param bool   $send
	 */
	public function removeViewer(Player $player, bool $send = true) : void{
		$id = spl_object_id($player);
		if(isset($this->viewers[$id])){
			if($send){
				Scoreboards::removeScoreboard($player, $this);

				$player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create($this->objective->objectiveName));
				$player->getNetworkSession()->sendDataPacket(SetScorePacket::remove($this->entries));
			}
			unset($this->viewers[$id]);
		}
	}
}
