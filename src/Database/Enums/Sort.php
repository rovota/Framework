<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Enums;

enum Sort: string
{

	case Asc = 'ASC';
	case Desc = 'DESC';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Sort::Asc => 'Ascending',
			Sort::Desc => 'Descending',
		};
	}

}