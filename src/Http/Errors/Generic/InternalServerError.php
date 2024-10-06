<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Generic;

use Rovota\Framework\Http\Error;

class InternalServerError extends Error
{

	protected int $code = 100;

	protected string $message = 'Something went wrong on our end. Try again later.';

}