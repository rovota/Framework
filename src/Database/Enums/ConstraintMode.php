<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Enums;

use Laminas\Db\Sql\Predicate\PredicateSet;

enum ConstraintMode: string
{

	case And = 'AND';
	case Or = 'OR';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			ConstraintMode::And => 'Ascending',
			ConstraintMode::Or => 'Descending',
		};
	}

	// -----------------

	public function realType(): string
	{
		return match ($this) {
			ConstraintMode::And => PredicateSet::OP_AND,
			ConstraintMode::Or => PredicateSet::OP_OR,
		};
	}

}