<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Enums;

enum SessionType: string
{

	case App = 'app';
	case Browser = 'browser';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			SessionType::App => 'App',
			SessionType::Browser => 'Browser',
		};
	}

}