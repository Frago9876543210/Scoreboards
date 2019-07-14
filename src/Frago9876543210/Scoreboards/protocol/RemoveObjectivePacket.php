<?php

declare(strict_types=1);

namespace Frago9876543210\Scoreboards\protocol;

class RemoveObjectivePacket extends \pocketmine\network\mcpe\protocol\RemoveObjectivePacket{

	public static function create(string $objectiveName) : self{
		$result = new self;
		$result->objectiveName = $objectiveName;
		return $result;
	}
}
