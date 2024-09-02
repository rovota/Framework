<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Laminas\Db\Adapter\AdapterInterface;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\InsertQuery;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;

final class Query
{

	protected AdapterInterface $adapter;

	protected QueryConfig $config;

	// -----------------

	public function __construct(AdapterInterface $adapter, mixed $options = [])
	{
		$this->adapter = $adapter;
		$this->config = $this->getConfigFromOptions($options);
	}

	// -----------------

	public function forTable(string $table): Query
	{
		$this->config->table = $table;
		return $this;
	}

	public function withModel(ModelInterface|string $model): Query
	{
		$this->config->model = $model;
		return $this;
	}

	// -----------------

	public function select(): SelectQuery
	{
		return new SelectQuery($this->adapter, $this->config);
	}

	public function update(): UpdateQuery
	{
		return new UpdateQuery($this->adapter, $this->config);
	}

	public function delete(): DeleteQuery
	{
		return new DeleteQuery($this->adapter, $this->config);
	}

	// -----------------

	public function insert(): InsertQuery
	{
		return new InsertQuery($this->adapter, $this->config);
	}

	// -----------------

	protected function getConfigFromOptions(mixed $options): QueryConfig
	{
		if ($options instanceof QueryConfig) {
			return $options;
		}

		$config = new QueryConfig();

		if ($options instanceof ModelInterface) {
			$config->model = $options;
		}

		if (is_array($options)) {
			foreach ($options as $name => $value) {
				$config->{$name} = $value;
			}
		}

		return $config;
	}

}