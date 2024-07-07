<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Interfaces;

interface Solution
{

	public function getTitle(): string;

	public function getDescription(): string;

	public function getDocumentationLinks(): array;

}