<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Update extends Action
{

	protected static string $name = 'update';

	protected static string $method = 'PATCH';

	protected static string $path = '/{id}/update';

}