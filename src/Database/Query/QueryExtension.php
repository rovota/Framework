<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\AbstractPreparableSql;
use Laminas\Db\Sql\Sql;
use Rovota\Framework\Database\CastingManager;
use Rovota\Framework\Database\Model\Model;

abstract class QueryExtension
{

	public AdapterInterface $adapter {
		get => $this->adapter;
	}

	public QueryConfig $config {
		get => $this->config;
	}

	public Sql $sql {
		get => $this->sql;
	}

	// -----------------

	public function __construct(AdapterInterface $adapter, QueryConfig $config)
	{
		$this->adapter = $adapter;
		$this->config = $config;
		$this->sql = new Sql($this->adapter);
	}

	// -----------------

	protected function fetchResult(AbstractPreparableSql $subject): ResultInterface
	{
		$statement = $this->sql->prepareStatementForSqlObject($subject);
		return $statement->execute();
	}

	// -----------------

	protected function normalizeValueForColumn(mixed $value, string $column): mixed
	{
		$model = $this->config->model ?? null;

		if ($model instanceof Model && $model->hasCast($column)) {
			return $model->castToRaw($column, $value);
		}
		return CastingManager::instance()->castToRawAutomatic($value);
	}

	// -----------------

	abstract protected function applyConfig(QueryConfig $config);

}