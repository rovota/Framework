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
use Rovota\Framework\Database\Enums\ConstraintMode;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
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
	 * Requires the presence of a `trashed` column, unless otherwise specified by a model.
	 */
	public function recover(): UpdateQuery
	{
		if ($this->config->model instanceof ModelInterface && defined($this->config->model::class . '::TRASHED_COLUMN')) {
			return $this->set([$this->config->model::TRASHED_COLUMN => null]);
		}

		return $this->set(['trashed' => null]);
	}

	/**
	 * Requires the presence of a `trashed` column, unless otherwise specified by a model.
	 */
	public function trash(): UpdateQuery
	{
		if ($this->config->model instanceof ModelInterface && defined($this->config->model::class . '::TRASHED_COLUMN')) {
			return $this->set([$this->config->model::TRASHED_COLUMN => now()]);
		}

		return $this->set(['trashed' => now()]);
	}

	// -----------------

	public function submit(): bool
	{
		return $this->fetchResult($this->update) instanceof ResultInterface;
	}

	// -----------------

	public function nest(Closure $callback, ConstraintMode $mode): UpdateQuery
	{
		$nested = new NestedQuery($this->getWherePredicate(), $this->config, $mode);

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