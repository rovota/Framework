<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Generic;

use Rovota\Framework\Http\Errors\Error;

class InternalServerError extends Error
{

	public int $code = 100;

	public string $message = 'Something went wrong on our end. Try again later.';

}