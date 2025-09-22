<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel\Enums;

enum EnvironmentType: string
{

	case Development = 'development';
	case Testing = 'testing';
	case Staging = 'staging';
	case Production = 'production';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			EnvironmentType::Development => 'Development',
			EnvironmentType::Testing => 'Testing',
			EnvironmentType::Staging => 'Staging',
			EnvironmentType::Production => 'Production',
		};
	}

}