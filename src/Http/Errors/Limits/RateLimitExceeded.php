<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Limits;

use Rovota\Framework\Http\Error;

class RateLimitExceeded extends Error
{

	public int $code = 101;

	public string $message = 'The rate limit for this token has been reached. Please try again later.';

}