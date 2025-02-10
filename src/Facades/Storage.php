<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Storage\Contents\Directory;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Storage\Enums\Driver;
use Rovota\Framework\Storage\Interfaces\DiskInterface;
use Rovota\Framework\Storage\StorageManager;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Facade;
use Rovota\Framework\Support\Moment;

/**
 * @method static DiskInterface disk(string|null $name = null)
 * @method static DiskInterface|null diskWithDriver(Driver $driver)
 * @method static DiskInterface create(array $config, string|null $name = null)
 *
 * @method static Sequence contents()
 * @method static Sequence files()
 * @method static Sequence directories()
 *
 * @method static bool exists(string $location)
 * @method static bool missing(string $location)
 *
 * @method static string checksum(string $location, array $config = [])
 *
 * @method static File file(string $location, array $without = [])
 * @method static Directory directory(string $location)
 *
 * @method static string|null read(string $location)
 * @method static bool write(string $location, string $contents)
 * @method static bool writeStream(string $location, mixed $contents)
 *
 * @method static bool move(string $from, string $to)
 * @method static bool rename(string $location, string $name)
 * @method static bool copy(string $from, string $to)
 *
 * @method static Moment lastModified(string $location)
 * @method static int size(string $location)
 * @method static string|null mimeType(string $location)
 *
 * @method static bool delete(string $location)
 * @method static bool deleteDirectory(string $location)
 * @method static bool clear(string $location)
 * @method static bool clearDirectory(string $location)
 *
 * @method static void prepend(string $location, string $contents, bool $new_line = true)
 * @method static void append(string $location, string $contents, bool $new_line = true)
 *
 * @method static bool isExtension(string $location, string $extension)
 * @method static bool isAnyExtension(string $location, array $extensions)
 * @method static bool isMimeType(string $location, string $mime_type)
 * @method static bool isAnyMimeType(string $location, array $mime_types)
 *
 * @method static bool isEqual(string $first, string $second)
 */
final class Storage extends Facade
{

	public static function service(): StorageManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return StorageManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'disk' => 'get',
			'diskWithDriver' => 'getWithDriver',
			'create' => 'createDisk',
			default => function (StorageManager $instance, string $method, array $parameters = []) {
				return $instance->get()->$method(...$parameters);
			},
		};
	}

}