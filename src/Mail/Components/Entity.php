<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Components;

use Rovota\Framework\Structures\Basic;

/**
 * @property-read string $name
 * @property-read string $address
 */
class Entity extends Basic
{

	public function __construct(string $name, string $address)
	{
		parent::__construct([
			'name' => trim($name),
			'address' => trim($address),
		]);
	}

}