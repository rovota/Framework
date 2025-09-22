<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing\Actions\Extensions;

use Rovota\Framework\Routing\Actions\Action;

final class Show extends Action
{

	protected static string $name = 'show';

	protected static string $method = 'GET';

	protected static string $path = '/{id}';

}