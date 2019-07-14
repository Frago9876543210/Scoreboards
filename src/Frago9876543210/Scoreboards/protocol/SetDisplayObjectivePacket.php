<?php

declare(strict_types=1);

namespace Frago9876543210\Scoreboards\protocol;

class SetDisplayObjectivePacket extends \pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket{

	public static function create(string $displaySlot, string $objectiveName, string $displayName, string $criteriaName, int $sortOrder) : self{
		$result = new self;
		$result->displaySlot = $displaySlot;
		$result->objectiveName = $objectiveName;
		$result->displayName = $displayName;
		$result->criteriaName = $criteriaName;
		$result->sortOrder = $sortOrder;
		return $result;
	}
}
