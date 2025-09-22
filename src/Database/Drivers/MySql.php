<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Drivers;

use Rovota\Framework\Database\Connection;
use Rovota\Framework\Database\ConnectionConfig;
use Rovota\Framework\Database\Handlers\MySqlHandler;

final class MySql extends Connection
{

	public function __construct(string $name, ConnectionConfig $config)
	{
		$handler = new MySqlHandler($config->parameters);

		parent::__construct($name, $handler, $config);
	}

}