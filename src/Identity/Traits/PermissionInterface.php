<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Identity\Traits;

interface PermissionInterface
{

	public function getIdentifier(): int;

	// -----------------

	public function getName(): string;

	public function getLabel(): string;

}