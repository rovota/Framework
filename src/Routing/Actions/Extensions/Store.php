<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Store extends Action
{

	protected static string $name = 'store';

	protected static string $method = 'POST';

	protected static string $path = '/store';

}