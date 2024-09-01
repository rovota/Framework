<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Laminas\Db\Sql\Predicate\Predicate;
use Rovota\Framework\Database\Traits\OrWhereQueryConstraints;
use Rovota\Framework\Database\Traits\WhereQueryConstraints;

final class NestedQuery
{
	use WhereQueryConstraints, OrWhereQueryConstraints;

	// -----------------
	protected Predicate $predicate;

	protected QueryConfig $config;

	// -----------------

	public function __construct(Predicate $predicate, QueryConfig $config)
	{
		$this->predicate = $predicate->nest();
		$this->config = $config;
	}

	// -----------------

	public function unnest(): Predicate
	{
		return $this->predicate->unnest();
	}

	// -----------------

	protected function getWherePredicate(): Predicate
	{
		return $this->predicate;
	}

}