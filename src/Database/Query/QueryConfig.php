<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Rovota\Framework\Database\Interfaces\ModelInterface;
use Rovota\Framework\Support\Config;

/**
 * @property string|array|null $table
 * @property ModelInterface|null $model
 */
final class QueryConfig extends Config
{

	protected function getTable(): string|array|null
	{
		return $this->get('table');
	}

	protected function setTable(string|array|null $name): void
	{
		if ($name === null) {
			$this->remove('table');
		}
		$this->set('table', $name);
	}

	protected function getModel(): ModelInterface|null
	{
		return $this->get('model');
	}

	protected function setModel(ModelInterface|null $model): void
	{
		if ($model === null) {
			$this->remove('model');
		}
		$this->set('model', $model);

		// TODO: Set table from model when provided
	}

}