<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

use Closure;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Select;
use Rovota\Framework\Database\Enums\Sort;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Query\NestedQuery;
use Rovota\Framework\Database\Query\QueryConfig;
use Rovota\Framework\Database\Query\QueryExtension;
use Rovota\Framework\Database\Traits\OrWhereQueryConstraints;
use Rovota\Framework\Database\Traits\WhereQueryConstraints;
use Rovota\Framework\Structures\Basket;

final class SelectQuery extends QueryExtension
{
	use WhereQueryConstraints, OrWhereQueryConstraints;

	// -----------------

	protected Select $select;

	// -----------------

	public function __construct(AdapterInterface $adapter, QueryConfig $config)
	{
		parent::__construct($adapter, $config);

		$this->select = $this->sql->select();
		$this->applyConfig($config);
	}

	// -----------------

	public function getSqlString(): string
	{
		return $this->select->getSqlString($this->adapter->platform);
	}

	// -----------------

	public function from(string|array $table): SelectQuery
	{
		$this->select->from($table);
		return $this;
	}

	// -----------------

	public function columns(array $columns): SelectQuery
	{
		$this->select->columns($columns);
		return $this;
	}

	// -----------------

	public function groupBy(string|array $columns): SelectQuery
	{
		$this->select->group($columns);
		return $this;
	}

	// -----------------

	public function orderBy(string|array $column, Sort $order = Sort::Asc): SelectQuery
	{
		if (is_string($column)) {
			$this->select->order(sprintf('%s %s', $column, $order->value));
		}

		if (is_array($column)) {
			foreach ($column as $key => $sorting) {
				if (is_numeric($key)) {
					$key = $sorting; $sorting = Sort::Asc;
				}
				$this->select->order(sprintf('%s %s', $key, $sorting->value));
			}
		}

		return $this;
	}

	/**
	 * Requires the presence of a `created` column, unless a different column is specified.
	 */
	public function latestFirst(string $column = 'created'): SelectQuery
	{
		return $this->orderBy($column, Sort::Desc);
	}

	/**
	 * Requires the presence of a `created` column, unless a different column is specified.
	 */
	public function oldestFirst(string $column = 'created'): SelectQuery
	{
		return $this->orderBy($column);
	}

	// -----------------

	public function limit(int $limit): SelectQuery
	{
		$this->select->limit($limit);
		return $this;
	}

	public function offset(int $offset = 0): SelectQuery
	{
		$this->select->offset($offset);
		return $this;
	}

	public function page(int $number, int $size = 10): SelectQuery
	{
		return $this->offset(($number - 1) * $size)->limit($size);
	}

	// -----------------

	public function find(string|int $identifier, string|null $column = null): ModelInterface|array|null
	{
		if ($column === null) {
			if ($this->config->model instanceof ModelInterface) {
				$column = $this->config->model->getPrimaryKey();
			} else {
				$column = 'id';
			}
		}

		return $this->where($column, $identifier)->first();
	}

	public function first(): ModelInterface|array|null
	{
		return $this->limit(1)->get()->first();
	}

	public function get(): Basket
	{
		$results = $this->fetchResult($this->select);
		$basket = new Basket();

		if ($results->count() > 0) {
			foreach ($results as $key => $result) {
				if ($this->config->model instanceof ModelInterface) {
					$result = $this->config->model::newFromQueryResult($result);
				}
				$basket->set($key, $result);
			}
		}

		return $basket;
	}

	// -----------------

	public function nest(Closure $callback): SelectQuery
	{
		$nested = new NestedQuery($this->getWherePredicate(), $this->config);

		$callback($nested);
		$this->select->where($nested->unnest());

		return $this;
	}

	// -----------------

	protected function getWherePredicate(): Predicate
	{
		return $this->select->where;
	}

	// -----------------

	protected function applyConfig(QueryConfig $config): void
	{
		if ($config->table !== null) {
			$this->from($config->table);
		}
	}

}