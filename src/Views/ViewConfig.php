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

class ViewConfig extends Config
{

	/**
	 * @var array<int, Link>
	 */
	public array $links {
		get => $this->array('links');
	}

	/**
	 * @var array<int, Meta>
	 */
	public array $meta {
		get => $this->array('meta');
	}

	/**
	 * @var array<int, Script>
	 */
	public array $scripts {
		get => $this->array('scripts');
	}

	// -----------------

	public array $variables {
		get => $this->array('variables');
	}

}