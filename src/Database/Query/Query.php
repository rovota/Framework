<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Laminas\Db\Adapter\AdapterInterface;
use Rovota\Framework\Database\Interfaces\ModelInterface;
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

	public function withModel(ModelInterface $model): Query
	{
		$this->config->model = $model;
		return $this;
	}

	// -----------------

	public function select(string|null $table = null): SelectQuery
	{
		if ($table !== null) {
			$this->config->table = $table;
		}
		return new SelectQuery($this->adapter, $this->config);
	}

	public function update(string|null $table = null): UpdateQuery
	{
		if ($table !== null) {
			$this->config->table = $table;
		}
		return new UpdateQuery($this->adapter, $this->config);
	}

	public function delete(string|null $table = null): DeleteQuery
	{
		if ($table !== null) {
			$this->config->table = $table;
		}
		return new DeleteQuery($this->adapter, $this->config);
	}

	// -----------------

	public function insert(string|null $table = null): InsertQuery
	{
		if ($table !== null) {
			$this->config->table = $table;
		}
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