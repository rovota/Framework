<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

use Closure;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Select;
use Rovota\Framework\Database\Enums\Sort;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Query\NestedQuery;
use Rovota\Framework\Database\Query\QueryConfig;
use Rovota\Framework\Database\Query\QueryExtension;
use Rovota\Framework\Database\Traits\OrWhereQueryConstraints;
use Rovota\Framework\Database\Traits\TrashQueryConstraints;
use Rovota\Framework\Database\Traits\WhereQueryConstraints;
use Rovota\Framework\Structures\Basket;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;

final class SelectQuery extends QueryExtension
{
	use TrashQueryConstraints, WhereQueryConstraints, OrWhereQueryConstraints, Conditionable;

	// -----------------

	protected Select $select;

	protected array $columns = [];

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
		foreach ($columns as $column => $value) {
			if (is_numeric($column)) {
				$this->column($value);
				continue;
			}

			$this->column($value, $column);
		}
		return $this;
	}

	public function column(Expression|string $expression, string|null $name = null): SelectQuery
	{
		if (is_string($expression) && Str::containsAny($expression, ['(', ')'])) {
			$expression = new Expression($expression);
		}

		if ($name === null) {
			$this->columns[] = $expression;
			return $this;
		}

		$this->columns[$name] = $expression;
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
	public function latestFirst(string|null $column = null): SelectQuery
	{
		if ($column === null && $this->config->model instanceof ModelInterface) {
			$column = $this->config->model->config->query_order_column;
		}
		return $this->orderBy($column ?? 'created', Sort::Desc);
	}

	/**
	 * Requires the presence of a `created` column, unless a different column is specified.
	 */
	public function oldestFirst(string|null $column = null): SelectQuery
	{
		if ($column === null && $this->config->model instanceof ModelInterface) {
			$column = $this->config->model->config->query_order_column;
		}
		return $this->orderBy($column ?? 'created');
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

	public function find(string|int|null $identifier, string|null $column = null): ModelInterface|array|null
	{
		if ($identifier === null) {
			return null;
		}

		if ($column === null) {
			if ($this->config->model instanceof ModelInterface) {
				$column = $this->config->model->config->primary_key;
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
		$this->applyTrashConstraints();

		if (empty($this->columns) === false) {
			$this->config->model = null;
			$this->select->columns($this->columns);
		}

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

	public function count(): int
	{
		$this->applyTrashConstraints();
		$this->columns(['count' => new Expression('COUNT(*)')]);

		$this->config->model = null;
		$this->select->columns($this->columns);

		$results = $this->fetchResult($this->select);
		$basket = new Basket();

		if ($results->count() > 0) {
			foreach ($results as $key => $result) {
				$basket->set($key, $result);
			}
		}

		return (int) $basket->sum('count');
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