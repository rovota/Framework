<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

use Closure;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Update;
use Rovota\Framework\Database\Query\NestedQuery;
use Rovota\Framework\Database\Query\QueryConfig;
use Rovota\Framework\Database\Query\QueryExtension;
use Rovota\Framework\Database\Traits\OrWhereQueryConstraints;
use Rovota\Framework\Database\Traits\WhereQueryConstraints;
use Rovota\Framework\Support\Traits\Conditionable;

final class UpdateQuery extends QueryExtension
{
	use WhereQueryConstraints, OrWhereQueryConstraints, Conditionable;

	// -----------------

	protected Update $update;

	// -----------------

	public function __construct(AdapterInterface $adapter, QueryConfig $config)
	{
		parent::__construct($adapter, $config);

		$this->update = $this->sql->update();
		$this->applyConfig($config);
	}

	// -----------------

	public function getSqlString(): string
	{
		return $this->update->getSqlString($this->adapter->platform);
	}

	// -----------------

	public function table(string $table): UpdateQuery
	{
		$this->update->table($table);
		return $this;
	}

	// -----------------

	public function set(array $data): UpdateQuery
	{
		foreach ($data as $column => $value) {
			if ($value === null) {
				continue;
			}
			$data[$column] = $this->normalizeValueForColumn($value, $column);
		}

		$this->update->set($data);
		return $this;
	}

	/**
	 * Requires the presence of a `deleted` column, unless a different column is specified.
	 */
	public function recover(string $column = 'deleted'): UpdateQuery
	{
		return $this->set([$column => null]);
	}

	/**
	 * Requires the presence of a `deleted` column, unless a different column is specified.
	 */
	public function trash(string $column = 'deleted'): UpdateQuery
	{
		return $this->set([$column => now()]);
	}

	// -----------------

	public function submit(): bool
	{
		return $this->fetchResult($this->update) instanceof ResultInterface;
	}

	// -----------------

	public function nest(Closure $callback): UpdateQuery
	{
		$nested = new NestedQuery($this->getWherePredicate(), $this->config);

		$callback($nested);
		$this->update->where($nested->unnest());

		return $this;
	}

	// -----------------

	protected function getWherePredicate(): Predicate
	{
		return $this->update->where;
	}

	// -----------------

	protected function applyConfig(QueryConfig $config): void
	{
		if ($config->table !== null) {
			$this->table($config->table);
		}
	}

}