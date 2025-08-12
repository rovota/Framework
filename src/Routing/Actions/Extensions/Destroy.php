<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Destroy extends Action
{

	protected static string $name = 'destroy';

	protected static string $method = 'PATCH';

	protected static string $path = '/{id}/destroy';

}