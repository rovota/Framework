<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Rovota\Framework\Database\ConnectionConfig;
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

	public function buildQuery(mixed $options = []): Query;

	public function buildQueryForTable(string $table): Query;

}