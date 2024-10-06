<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Generic;

use Rovota\Framework\Http\Error;

class ResourceNotFound extends Error
{

	protected int $code = 300;

	protected string $message = 'No resource could be found matching the provided parameters.';

}