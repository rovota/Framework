<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Enums;

enum Driver: string
{

	case MySql = 'mysql';
//	case PostgreSql = 'postgresql';
//	case SqLite = 'sqlite';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::MySql => 'MySQL',
//			Driver::PostgreSql => 'PostgreSQL',
//			Driver::SqLite => 'SQLite',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::MySql => 'Connect to a database using MySQL or MariaDB.',
//			Driver::PostgreSql => 'Connect to a database using PostgreSQL.',
//			Driver::SqLite => 'Connect to a database using SQLite.',
		};
	}

	// -----------------

	public function realName(): string
	{
		return match ($this) {
			Driver::MySql => 'Pdo_Mysql',
//			Driver::PostgreSql => 'Pdo_Pgsql',
//			Driver::SqLite => 'Pdo_Sqlite',
		};
	}

}