<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Interfaces;

interface Arrayable
{

	/**
	 * Returns an array representation of the instance.
	 */
	public function toArray(): array;

}