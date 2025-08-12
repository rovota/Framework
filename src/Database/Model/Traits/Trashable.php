<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model\Traits;

use Rovota\Framework\Database\Query\Extensions\SelectQuery;

/**
 * @property array $attributes
 *
 * @method mixed getAttribute(string $name)
 */
trait Trashable
{
	const string TRASHED_COLUMN = 'trashed';

	// -----------------

	public function isTrashed(): bool
	{
		return $this->getAttribute(static::TRASHED_COLUMN) !== null;
	}

	// -----------------

	public function trash(): bool
	{
		$result = $this->getUpdateQuery()->trash()->submit();

		if ($result) {
			$this->attributes[static::TRASHED_COLUMN] = now();
			return true;
		}

		return false;
	}

	public function recover(): bool
	{
		$result = $this->getUpdateQuery()->recover()->submit();

		if ($result) {
			$this->attributes[static::TRASHED_COLUMN] = null;
			return true;
		}

		return false;
	}

	// -----------------

	public static function withTrashed(): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->withTrashed();
	}

	public static function onlyTrashed(): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->onlyTrashed();
	}

}