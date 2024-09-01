<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

final class MultiInsertQuery
{

	/**
	 * @var array<int, InsertQuery>
	 */
	protected array $rows;

	// -----------------

	public function __construct(array $rows)
	{
		$this->rows = $rows;
	}

	// -----------------

	public function submit(): bool
	{
		foreach ($this->rows as $row) {
			if ($row->submit() === false) {
				return false;
			}
		}

		return true;
	}

}