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
		return array_all($this->rows, fn($row) => $row->submit() === true);
	}

}