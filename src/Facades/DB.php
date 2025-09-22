<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Database\Connection;
use Rovota\Framework\Database\ConnectionManager;
use Rovota\Framework\Database\Query\Extensions\DeleteQuery;
use Rovota\Framework\Database\Query\Extensions\InsertQuery;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;
use Rovota\Framework\Database\Query\Extensions\UpdateQuery;
use Rovota\Framework\Database\Query\Query;
use Rovota\Framework\Support\Facade;

/**
 * @method static Connection connection(string|null $name = null)
 * @method static Connection create(array $config, string|null $name = null)
 *
 * @method static Query query(mixed $options = [])
 * @method static Query queryForTable(string $table)
 *
 * @method static SelectQuery select()
 * @method static UpdateQuery update()
 * @method static DeleteQuery delete()
 * @method static InsertQuery insert()
 */
final class DB extends Facade
{

	public static function service(): ConnectionManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return ConnectionManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'connection' => 'get',
			'create' => 'createConnection',
			default => function (ConnectionManager $instance, string $method, array $parameters = []) {
				return $instance->get()->$method(...$parameters);
			},
		};
	}

}