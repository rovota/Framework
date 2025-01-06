<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model\Traits;

use Rovota\Framework\Database\Enums\Sort;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Structures\Basket;

/**
 * @method static SelectQuery whereExpression(string $expression, array $parameters)
 * @method static SelectQuery where(string|array $column, mixed $value = null)
 * @method static SelectQuery whereEqual(string $column, mixed $value)
 * @method static SelectQuery whereNotEqual(string $column, mixed $value)
 * @method static SelectQuery whereLessThan(string $column, mixed $value)
 * @method static SelectQuery whereGreaterThan(string $column, mixed $value)
 * @method static SelectQuery whereBefore(string $column, mixed $value)
 * @method static SelectQuery whereAfter(string $column, mixed $value)
 * @method static SelectQuery whereLike(string $column, mixed $value)
 * @method static SelectQuery whereNotLike(string $column, mixed $value)
 * @method static SelectQuery whereFullText(string|array $column, string $value)
 * @method static SelectQuery whereNull(string $column)
 * @method static SelectQuery whereNotNull(string $column)
 * @method static SelectQuery whereIn(string $column, array $values)
 * @method static SelectQuery whereNotIn(string $column, array $values)
 * @method static SelectQuery whereBetween(string $column, mixed $start, mixed $end)
 * @method static SelectQuery whereNotBetween(string $column, mixed $start, mixed $end)
 */
trait ModelQueryFunctions
{

	public function insert(array $data): bool
	{
		return static::getQueryBuilderFromStaticModel()->insert()->data($data)->submit();
	}

	public function insertMultiple(array $rows): bool
	{
		return static::getQueryBuilderFromStaticModel()->insert()->rows($rows)->submit();
	}

	// -----------------

	public static function orderBy(string|array $column, Sort $order = Sort::Asc): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->orderBy($column, $order);
	}

	public static function latestFirst(string|null $column = null): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->latestFirst($column);
	}

	public static function oldestFirst(string|null $column = null): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->oldestFirst($column);
	}

	// -----------------

	public function limit(int $limit): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->limit($limit);
	}

	public function offset(int $offset = 0): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->offset($offset);
	}

	public function page(int $number, int $size = 10): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->page($number, $size);
	}

	// -----------------

	public static function select(): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select();
	}

	public static function find(string|int $identifier, string|null $column = null): static|null
	{
		return static::getQueryBuilderFromStaticModel()->select()->find($identifier, $column);
	}

	public static function all(): Basket
	{
		return static::getQueryBuilderFromStaticModel()->select()->get();
	}

	public static function count(): float|int
	{
		return static::getQueryBuilderFromStaticModel()->select()->count();
	}

	// -----------------

	public static function update(array $data = []): UpdateQuery
	{
		$query = static::getQueryBuilderFromStaticModel()->update();

		if (empty($data) === false) {
			$query->set($data);
		}

		return $query;
	}

}