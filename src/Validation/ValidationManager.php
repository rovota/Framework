<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation;

use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Validation\Interfaces\ValidatorInterface;
use Rovota\Framework\Validation\Rules\RuleManager;

/**
 * @internal
 */
final class ValidationManager extends ServiceProvider
{
	/**
	 * @var Map<string, ValidatorInterface>
	 */
	protected Map $validators;

	// -----------------

	public function __construct()
	{
		$this->validators = new Map();

		RuleManager::initialize();
	}

}