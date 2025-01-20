<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Handlers;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Rovota\Framework\Database\Enums\Driver;
use Rovota\Framework\Database\Interfaces\ConnectionHandlerInterface;
use Rovota\Framework\Support\Config;

class MySqlHandler implements ConnectionHandlerInterface
{

	public AdapterInterface $adapter {
		get => $this->adapter;
	}

	protected array $tables = [];

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->adapter = new Adapter(
			array_merge([
				'driver' => Driver::MySql->realName(),
				'charset' => 'utf8mb4',
			], $this->getFilteredParameters($parameters))
		);
		
		if ($this->hasTimezoneData()) {
			$this->setTimezone('UTC');
		}
	}

	// -----------------

	public function getVersion(): string
	{
		$result = $this->adapter->query('SELECT VERSION() as version')->execute();

		if ($result instanceof ResultInterface) {
			return $result->current()['version'];
		}

		return 'Unknown';
	}

	public function getTables(): array
	{
		if (empty($this->tables) === false) {
			return $this->tables;
		}

		$result = $this->adapter->query('SHOW TABLES')->execute();

		if ($result instanceof ResultInterface) {
			foreach ($result as $row) {
				$this->tables[] = array_shift($row);
			}
		}

		return $this->tables;
	}

	public function hasTable(string $table): bool
	{
		return in_array($table, $this->getTables());
	}

	// -----------------

	public function setTimezone(string $timezone): void
	{
		$this->adapter->createStatement('SET time_zone = ?', [$timezone])->execute();
	}

	public function hasTimezoneData(): bool
	{
		$result = $this->adapter->query("SELECT CONVERT_TZ('2000-01-01 1:00:00','UTC','Europe/Amsterdam') AS time")->execute();

		if ($result instanceof ResultInterface) {
			return $result->current()['time'] !== null;
		}

		return false;
	}

	// -----------------

	public function getLastId(): string|int
	{
		$result = $this->adapter->query('SELECT LAST_INSERT_ID() as last_id')->execute();

		if ($result instanceof ResultInterface) {
			return $result->current()['last_id'];
		}

		return 0;
	}

	// -----------------

	protected function getFilteredParameters(Config $parameters): array
	{
		return $parameters->filter(function ($value) {
			return $value !== null;
		})->toArray();
	}

}