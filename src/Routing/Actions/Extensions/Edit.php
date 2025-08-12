<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Edit extends Action
{

	protected static string $name = 'edit';

	protected static string $method = 'GET';

	protected static string $path = '/{id}/edit';

}