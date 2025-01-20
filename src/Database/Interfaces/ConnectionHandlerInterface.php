<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

use Laminas\Db\Adapter\AdapterInterface;

interface ConnectionHandlerInterface
{

	public AdapterInterface $adapter {
		get;
	}

	// -----------------

	public function getVersion(): string;

	public function getTables(): array;

	public function hasTable(string $table): bool;

	// -----------------

	public function setTimezone(string $timezone): void;

	public function hasTimezoneData(): bool;

	// -----------------

	public function getLastId(): string|int;

}