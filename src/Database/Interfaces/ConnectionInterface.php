<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

use Rovota\Framework\Database\ConnectionConfig;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\InsertQuery;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Database\Query\Query;

interface ConnectionInterface
{

	public string $name {
		get;
	}

	public ConnectionConfig $config {
		get;
	}

	public ConnectionHandlerInterface $handler {
		get;
	}

	// -----------------

	public function __toString(): string;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function query(mixed $options = []): Query;

	public function queryForTable(string $table): Query;

	// -----------------

	public function select(): SelectQuery;

	public function update(): UpdateQuery;

	public function delete(): DeleteQuery;

	public function insert(): InsertQuery;

	// -----------------

	public function tables(): array;

	public function hasTable(string $name): bool;

	// -----------------

	public function lastId(): string|int;

}