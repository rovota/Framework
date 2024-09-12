<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Rovota\Framework\Database\ConnectionConfig;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\InsertQuery;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Database\Query\Query;

interface ConnectionInterface
{

	public function __toString(): string;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function getName(): string;

	public function getConfig(): ConnectionConfig;

	public function getHandler(): ConnectionHandlerInterface;

	// -----------------

	public function getAdapter(): AdapterInterface;

	public function getPlatform(): PlatformInterface;

	public function getSchema(): string;

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