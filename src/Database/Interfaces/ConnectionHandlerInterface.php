<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Interfaces;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Platform\PlatformInterface;

interface ConnectionHandlerInterface
{

	public function getAdapter(): AdapterInterface;

	public function getPlatform(): PlatformInterface;

	public function getSchema(): string;

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