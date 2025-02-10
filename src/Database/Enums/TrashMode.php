<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum TrashMode: int
{
	use EnumHelpers;

	case None = 0;
	case Only = 1;
	case Mixed = 2;

}