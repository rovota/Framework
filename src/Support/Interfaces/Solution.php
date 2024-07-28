<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Interfaces;

interface Solution
{

	public function title(): string;

	public function description(): string;

	public function references(): array;

}