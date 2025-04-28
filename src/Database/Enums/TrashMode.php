<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Enums;

enum TrashMode: int
{

	case None = 0;
	case Only = 1;
	case Mixed = 2;

}