<?php

declare(strict_types=1);

namespace Frago9876543210\Scoreboards\protocol;

class SetScorePacket extends \pocketmine\network\mcpe\protocol\SetScorePacket{

	public static function change(array $entries) : self{
		$result = new self;
		$result->type = self::TYPE_CHANGE;
		$result->entries = $entries;
		return $result;
	}

	public static function remove(array $entries) : self{
		$result = new self;
		$result->type = self::TYPE_REMOVE;
		$result->entries = $entries;
		return $result;
	}
}
