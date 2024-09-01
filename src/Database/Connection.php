<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Rovota\Framework\Database\Interfaces\ConnectionHandlerInterface;
use Rovota\Framework\Database\Interfaces\ConnectionInterface;
use Rovota\Framework\Database\Query\Query;

abstract class Connection implements ConnectionInterface
{

	protected string $name;

	protected ConnectionConfig $config;

	protected ConnectionHandlerInterface $handler;

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
		return ConnectionManager::instance()->getDefault() === $this->name;
	}

	// -----------------

	public function getName(): string
	{
		return $this->name;
	}

	public function getConfig(): ConnectionConfig
	{
		return $this->config;
	}

	public function getHandler(): ConnectionHandlerInterface
	{
		return $this->handler;
	}

	// -----------------

	public function getAdapter(): AdapterInterface
	{
		return $this->handler->getAdapter();
	}

	public function getPlatform(): PlatformInterface
	{
		return $this->handler->getPlatform();
	}

	public function getSchema(): string
	{
		return $this->handler->getSchema();
	}

	// -----------------

	public function buildQuery(mixed $options = []): Query
	{
		return new Query($this->handler->getAdapter(), $options);
	}

	public function buildQueryForTable(string $table): Query
	{
		return new Query($this->handler->getAdapter(), ['table' => $table]);
	}

}