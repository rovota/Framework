<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum RequestMethod: string
{
	use EnumHelpers;

	case Get = 'GET';
	case Head = 'HEAD';
	case Post = 'POST';
	case Put = 'PUT';
	case Delete = 'DELETE';
	case Connect = 'CONNECT';
	case Options = 'OPTIONS';
	case Trace = 'TRACE';
	case Patch = 'PATCH';

	// -----------------

	public function label(): string
	{
		return $this->value;
	}

}