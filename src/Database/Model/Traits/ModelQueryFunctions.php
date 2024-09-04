<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model\Traits;

use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Structures\Basket;

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

	// -----------------

	public static function whereExpression(string $expression, array $parameters): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->select()->whereExpression($expression, $parameters);
	}

	// -----------------

	public static function where(string|array $column, mixed $value = null): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->where($column, $value);
	}

	// -----------------

	public static function whereEqual(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereEqual($column, $value);
	}

	public static function whereNotEqual(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNotEqual($column, $value);
	}

	// -----------------

	public static function whereLessThan(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereLessThan($column, $value);
	}

	public static function whereGreaterThan(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereGreaterThan($column, $value);
	}

	// -----------------

	public static function whereLike(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereLike($column, $value);
	}

	public static function whereNotLike(string $column, mixed $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNotLike($column, $value);
	}

	// -----------------

	public static function whereFullText(string|array $column, string $value): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereFullText($column, $value);
	}

	// -----------------

	public static function whereNull(string $column): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNull($column);
	}

	public static function whereNotNull(string $column): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNotNull($column);
	}

	// -----------------

	public static function whereIn(string $column, array $values): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereIn($column, $values);
	}

	public static function whereNotIn(string $column, array $values): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNotIn($column, $values);
	}

	// -----------------

	public static function whereBetween(string $column, mixed $start, mixed $end): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereBetween($column, $start, $end);
	}

	public static function whereNotBetween(string $column, mixed $start, mixed $end): SelectQuery
	{
		return static::getQueryBuilderFromStaticModel()->whereNotBetween($column, $start, $end);
	}

}