<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

use Closure;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Predicate\Predicate;
use Rovota\Framework\Database\Enums\ConstraintMode;
use Rovota\Framework\Database\Query\NestedQuery;
use Rovota\Framework\Database\Query\QueryConfig;
use Rovota\Framework\Database\Query\QueryExtension;
use Rovota\Framework\Database\Traits\OrWhereQueryConstraints;
use Rovota\Framework\Database\Traits\WhereQueryConstraints;
use Rovota\Framework\Support\Traits\Conditionable;

final class DeleteQuery extends QueryExtension
{
	use WhereQueryConstraints, OrWhereQueryConstraints, Conditionable;

	// -----------------

	protected Delete $delete;

	// -----------------

	public function __construct(AdapterInterface $adapter, QueryConfig $config)
	{
		parent::__construct($adapter, $config);

		$this->delete = $this->sql->delete();
		$this->applyConfig($config);
	}

	// -----------------

	public function getSqlString(): string
	{
		return $this->delete->getSqlString($this->adapter->platform);
	}

	// -----------------

	public function from(string $table): DeleteQuery
	{
		$this->delete->from($table);
		return $this;
	}

	// -----------------

	public function submit(): bool
	{
		return $this->fetchResult($this->delete) instanceof ResultInterface;
	}

	// -----------------

	public function nest(Closure $callback, ConstraintMode $mode): DeleteQuery
	{
		$nested = new NestedQuery($this->getWherePredicate(), $this->config, $mode);

		$callback($nested);
		$this->delete->where($nested->unnest());

		return $this;
	}

	// -----------------

	protected function getWherePredicate(): Predicate
	{
		return $this->delete->where;
	}

	// -----------------

	protected function applyConfig(QueryConfig $config): void
	{
		if ($config->table !== null) {
			$this->from($config->table);
		}
	}

}