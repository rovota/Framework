<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Create extends Action
{

	protected static string $name = 'create';

	protected static string $method = 'GET';

	protected static string $path = '/create';

}