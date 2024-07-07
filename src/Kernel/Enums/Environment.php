<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum Environment: string
{
	use EnumHelpers;

	case Development = 'development';
	case Testing = 'testing';
	case Staging = 'staging';
	case Production = 'production';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Environment::Development => 'Development',
			Environment::Testing => 'Testing',
			Environment::Staging => 'Staging',
			Environment::Production => 'Production',
		};
	}

}