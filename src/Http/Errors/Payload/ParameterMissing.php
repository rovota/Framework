<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Errors\Payload;

use Rovota\Framework\Http\Errors\Error;

class ParameterMissing extends Error
{

	public int $code = 400;

	public string $message = 'A required parameter is missing. Check the documentation for more information.';

	// -----------------

	public function __construct(string|null $message = null, array $parameters = [], int $code = 0)
	{
		if (count($parameters) === 1) {
			$this->message = 'The required parameter "%s" is missing. Check the documentation for more information.';
		}

		parent::__construct($message, $parameters, $code);
	}

}