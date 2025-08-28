<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Interfaces\ConnectionHandlerInterface;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\InsertQuery;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Database\Query\Query;

abstract class Connection
{

	public string $name {
		get => $this->name;
	}

	public ConnectionConfig $config {
		get => $this->config;
	}

	public ConnectionHandlerInterface $handler {
		get => $this->handler;
	}

	// -----------------

	public function __construct(string $name, ConnectionHandlerInterface $handler, ConnectionConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->handler = $handler;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return ConnectionManager::instance()->default === $this->name;
	}

	// -----------------

	public function query(mixed $options = []): Query
	{
		return new Query($this->handler->adapter, $options);
	}

	public function queryForTable(string $table): Query
	{
		return new Query($this->handler->adapter, ['table' => $table]);
	}

	// -----------------

	public function select(): SelectQuery
	{
		return $this->query()->select();
	}

	public function update(): UpdateQuery
	{
		return $this->query()->update();
	}

	public function delete(): DeleteQuery
	{
		return $this->query()->delete();
	}

	public function insert(): InsertQuery
	{
		return $this->query()->insert();
	}

	// -----------------

	public function tables(): array
	{
		return $this->handler->getTables();
	}

	public function hasTable(string $name): bool
	{
		return $this->handler->hasTable($name);
	}

	// -----------------

	public function lastId(): string|int
	{
		return $this->handler->getLastId();
	}

	// -----------------

	public function beginTransaction(): bool
	{
		$this->handler->adapter->getDriver()->getConnection()->beginTransaction();
		return true;
	}

	public function commit(): bool
	{
		$this->handler->adapter->getDriver()->getConnection()->commit();
		return true;
	}

	public function rollback(): bool
	{
		$this->handler->adapter->getDriver()->getConnection()->rollback();
		return true;
	}

}