<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Support\Config;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;

/**
 * @property-read array<int, Link> $links
 * @property-read array<int, Meta> $meta
 * @property-read array<int, Script> $scripts
 *
 * @property-read array $variables
 */
class ViewConfig extends Config
{

	protected function getLinks(): array
	{
		return $this->array('links');
	}

	protected function getMeta(): array
	{
		return $this->array('meta');
	}

	protected function getScripts(): array
	{
		return $this->array('scripts');
	}

	// -----------------

	protected function getVariables(): array
	{
		return $this->array('variables');
	}

}