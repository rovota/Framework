<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Traits;

use Closure;
use Rovota\Framework\Database\Enums\ConstraintMode;
use Rovota\Framework\Database\Query\NestedQuery;

trait OrWhereQueryConstraints
{

	public function or(Closure $callback): static
	{
		return $this->nest($callback, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereExpression(string $expression, mixed $parameters = null): static
	{
		return $this->whereExpression($expression, $parameters, ConstraintMode::Or);
	}

	// -----------------

	public function orWhere(Closure|string|array $column, mixed $value = null): static
	{
		if ($column instanceof Closure) {
			$this->orWhere($column);
			return $this;
		}

		if (is_array($column)) {
			foreach ($column as $col => $value) {
				$this->orWhere($col, $value);
			}
			return $this;
		}

		return $this->orWhereEqual($column, $value);
	}

	// -----------------

	public function orWhereEqual(string $column, mixed $value): static
	{
		return $this->whereEqual($column, $value, ConstraintMode::Or);
	}

	public function orWhereNotEqual(string $column, mixed $value): static
	{
		return $this->whereNotEqual($column, $value, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereLessThan(string $column, mixed $value): static
	{
		return $this->whereLessThan($column, $value, ConstraintMode::Or);
	}

	public function orWhereGreaterThan(string $column, mixed $value): static
	{
		return $this->whereGreaterThan($column, $value, ConstraintMode::Or);
	}

	public function orWhereBefore(string $column, mixed $value): static
	{
		return $this->orWhereLessThan($column, $value);
	}

	public function orWhereAfter(string $column, mixed $value): static
	{
		return $this->orWhereGreaterThan($column, $value);
	}

	// -----------------

	public function orWhereLike(string $column, mixed $value): static
	{
		return $this->whereLike($column, $value, ConstraintMode::Or);
	}

	public function orWhereNotLike(string $column, mixed $value): static
	{
		return $this->whereNotLike($column, $value, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereFullText(string|array $column, string $value): static
	{
		return $this->whereFullText($column, $value, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereNull(string|array $column): static
	{
		return $this->whereNull($column, ConstraintMode::Or);
	}

	public function orWhereNotNull(string|array $column): static
	{
		return $this->whereNotNull($column, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereIn(string $column, array $values): static
	{
		return $this->whereIn($column, $values, ConstraintMode::Or);
	}

	public function orWhereNotIn(string $column, array $values): static
	{
		return $this->whereNotIn($column, $values, ConstraintMode::Or);
	}

	// -----------------

	public function orWhereBetween(string $column, mixed $start, mixed $end): static
	{
		return $this->whereBetween($column, $start, $end, ConstraintMode::Or);
	}

	public function orWhereBetweenColumns(string $value, array $columns): static
	{
		$this->or(function (NestedQuery $query) use ($value, $columns) {
			$query->whereLessThan($columns[0], $value)->whereGreaterThan($columns[1], $value);
		});

		return $this;
	}

	public function orWhereNotBetween(string $column, mixed $start, mixed $end): static
	{
		return $this->whereNotBetween($column, $start, $end, ConstraintMode::Or);
	}

	public function orWhereNotBetweenColumns(string $value, array $columns): static
	{
		$this->or(function (NestedQuery $query) use ($value, $columns) {
			$query->whereGreaterThan($columns[0], $value)->orWhereLessThan($columns[1], $value);
		});

		return $this;
	}

	// -----------------

	public function orWhereListHas(string $column, mixed $value): static
	{
		return $this->whereListHas($column, $value, ConstraintMode::Or);
	}

}