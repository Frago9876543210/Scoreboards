<?php

declare(strict_types=1);

namespace Frago9876543210\Scoreboards\protocol\types;

class ScorePacketEntry extends \pocketmine\network\mcpe\protocol\types\ScorePacketEntry{

	public static function create(int $entryUniqueId, string $objectiveName, int $score, string $customName) : self{
		$result = new self;
		$result->scoreboardId = $entryUniqueId;
		$result->objectiveName = $objectiveName;
		$result->score = $score;
		$result->type = self::TYPE_FAKE_PLAYER;
		$result->customName = $customName;
		return $result;
	}
}
