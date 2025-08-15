<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Traits;

use Closure;
use Laminas\Db\Sql\Predicate\Between;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Predicate\IsNotNull;
use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\NotBetween;
use Laminas\Db\Sql\Predicate\NotIn;
use Laminas\Db\Sql\Predicate\NotLike;
use Laminas\Db\Sql\Predicate\Operator;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\Enums\ConstraintMode;
use Rovota\Framework\Database\Query\NestedQuery;
use Rovota\Framework\Support\Str;

trait WhereQueryConstraints
{

	public function and(Closure $callback): static
	{
		return $this->nest($callback, ConstraintMode::And);
	}

	// -----------------

	public function whereExpression(string $expression, array $parameters, ConstraintMode $mode = ConstraintMode::And): static
	{
		foreach ($parameters as $key => $value) {
			$parameters[$key] = CastingManager::instance()->castToRawAutomatic($value);
		}

		$this->getWherePredicate()->addPredicate(
			new Expression($expression, $parameters), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function where(string|array $column, mixed $value = null, ConstraintMode $mode = ConstraintMode::And): static
	{
		if (is_array($column)) {
			foreach ($column as $col => $value) {
				$this->where($col, $value, $mode);
			}
			return $this;
		}

		return $this->whereEqual($column, $value, $mode);
	}

	// -----------------

	public function whereEqual(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		$this->getWherePredicate()->addPredicate(
			new Operator($column, Operator::OPERATOR_EQUAL_TO, $value), $mode->realType()
		);

		return $this;
	}

	public function whereNotEqual(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		$this->getWherePredicate()->addPredicate(
			new Operator($column, Operator::OPERATOR_NOT_EQUAL_TO, $value), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function whereLessThan(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		$this->getWherePredicate()->addPredicate(
			new Operator($column, Operator::OPERATOR_LESS_THAN, $value), $mode->realType()
		);

		return $this;
	}

	public function whereGreaterThan(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		$this->getWherePredicate()->addPredicate(
			new Operator($column, Operator::OPERATOR_GREATER_THAN, $value), $mode->realType()
		);

		return $this;
	}

	public function whereBefore(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		return $this->whereLessThan($column, $value, $mode);
	}

	public function whereAfter(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		return $this->whereGreaterThan($column, $value, $mode);
	}

	// -----------------

	public function whereLike(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		if (Str::contains($value, '%') === false) {
			$value = Str::wrap($value, '%');
		}

		$this->getWherePredicate()->addPredicate(
			new Like($column, $value), $mode->realType()
		);

		return $this;
	}

	public function whereNotLike(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		if (Str::contains($value, '%') === false) {
			$value = Str::wrap($value, '%');
		}

		$this->getWherePredicate()->addPredicate(
			new NotLike($column, $value), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function whereFullText(string|array $column, string $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		if (is_array($column)) {
			$column = implode(',', $column);
		}

		$this->getWherePredicate()->addPredicate(
			new Expression(sprintf('MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE)', $column), [$value]), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function whereNull(string|array $column, ConstraintMode $mode = ConstraintMode::And): static
	{
		if (is_array($column)) {
			foreach ($column as $col) {
				$this->whereNull($col, $mode);
			}
			return $this;
		}

		$this->getWherePredicate()->addPredicate(
			new IsNull($column), $mode->realType()
		);

		return $this;
	}

	public function whereNotNull(string|array $column, ConstraintMode $mode = ConstraintMode::And): static
	{
		if (is_array($column)) {
			foreach ($column as $col) {
				$this->whereNotNull($col, $mode);
			}
			return $this;
		}

		$this->getWherePredicate()->addPredicate(
			new IsNotNull($column), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function whereIn(string $column, array $values, ConstraintMode $mode = ConstraintMode::And): static
	{
		foreach ($values as $key => $value) {
			$values[$key] = $this->normalizeValueForColumn($value, $column);
		}

		$this->getWherePredicate()->addPredicate(
			new In($column, $values), $mode->realType()
		);

		return $this;
	}

	public function whereNotIn(string $column, array $values, ConstraintMode $mode = ConstraintMode::And): static
	{
		foreach ($values as $key => $value) {
			$values[$key] = $this->normalizeValueForColumn($value, $column);
		}

		$this->getWherePredicate()->addPredicate(
			new NotIn($column, $values), $mode->realType()
		);

		return $this;
	}

	// -----------------

	public function whereBetween(string $column, mixed $start, mixed $end, ConstraintMode $mode = ConstraintMode::And): static
	{
		$start = $this->normalizeValueForColumn($start, $column);
		$end = $this->normalizeValueForColumn($end, $column);

		$this->getWherePredicate()->addPredicate(
			new Between($column, $start, $end), $mode->realType()
		);

		return $this;
	}

	public function whereBetweenColumns(string $value, array $columns, ConstraintMode $mode = ConstraintMode::And): static
	{
		$this->nest(function (NestedQuery $query) use ($value, $columns) {
			$query->whereLessThan($columns[0], $value)->whereGreaterThan($columns[1], $value);
		}, $mode);

		return $this;
	}

	public function whereNotBetween(string $column, mixed $start, mixed $end, ConstraintMode $mode = ConstraintMode::And): static
	{
		$start = $this->normalizeValueForColumn($start, $column);
		$end = $this->normalizeValueForColumn($end, $column);

		$this->getWherePredicate()->addPredicate(
			new NotBetween($column, $start, $end), $mode->realType()
		);

		return $this;
	}

	public function whereNotBetweenColumns(string $value, array $columns, ConstraintMode $mode = ConstraintMode::And): static
	{
		$this->nest(function (NestedQuery $query) use ($value, $columns) {
			$query->whereGreaterThan($columns[0], $value)->orWhereLessThan($columns[1], $value);
		}, $mode);

		return $this;
	}

	// -----------------

	public function whereListHas(string $column, mixed $value, ConstraintMode $mode = ConstraintMode::And): static
	{
		$value = $this->normalizeValueForColumn($value, $column);

		$this->getWherePredicate()->addPredicate(
			new Expression(sprintf('FIND_IN_SET(?, %s)', trim($column)), [$value]), $mode->realType()
		);

		return $this;
	}

}