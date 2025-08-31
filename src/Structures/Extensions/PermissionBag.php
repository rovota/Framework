<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures\Extensions;

use Rovota\Framework\Identity\Models\Permission;
use Rovota\Framework\Structures\Bucket;

class PermissionBag extends Bucket
{

	public function retrieve(string|int $identifier): Permission|null
	{
		if (is_int($identifier)) {
			$haystack = $this->map(function (Permission $permission) {
				return $permission->id;
			})->toArray();

			$identifier = array_search($identifier, $haystack);
			if ($identifier === false) {
				return null;
			}
		}

		return $this->get($identifier);
	}

	// -----------------

	public function has(mixed $key): bool
	{
		if ($this->isEmpty()) {
			return false;
		}

		return array_any(is_array($key) ? $key : [$key], function ($identifier) {
			return $this->retrieve($identifier) !== null;
		});
	}

}