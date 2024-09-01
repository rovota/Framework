<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query\Extensions;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Insert;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\Query\QueryConfig;
use Rovota\Framework\Database\Query\QueryExtension;

final class InsertQuery extends QueryExtension
{

	protected Insert $insert;

	// -----------------

	public function __construct(AdapterInterface $adapter, QueryConfig $config)
	{
		parent::__construct($adapter, $config);

		$this->insert = $this->sql->insert();
		$this->applyConfig($config);
	}

	// -----------------

	public function getSqlString(): string
	{
		return $this->insert->getSqlString($this->adapter->platform);
	}

	// -----------------

	public function into(string $table): InsertQuery
	{
		$this->insert->into($table);
		return $this;
	}

	// -----------------

	public function data(array $data): InsertQuery
	{
		foreach ($data as $column => $value) {
			if ($value === null) {
				unset($data[$column]);
			}
			$data[$column] = CastingManager::normalizeValueForColumn($value, $column, $this->config->model);
		}

		$this->insert->values($data);
		return $this;
	}

	public function rows(array $rows): MultiInsertQuery
	{
		foreach ($rows as $key => $row) {
			$rows[$key] = new InsertQuery($this->adapter, $this->config);
			$rows[$key]->data($row);
		}

		return new MultiInsertQuery($rows);
	}

	// -----------------

	public function submit(): bool
	{
		return $this->fetchResult($this->insert) instanceof ResultInterface;
	}

	// -----------------

	protected function applyConfig(QueryConfig $config): void
	{
		if ($config->table !== null) {
			$this->into($config->table);
		}
	}

}