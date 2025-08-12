<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Index extends Action
{

	protected static string $name = 'index';

	protected static string $method = 'GET';

	protected static string $path = '/';

}