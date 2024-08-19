<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Text;

/**
 * @internal
 */
final class RegistryManager
{

	protected Bucket $entries;

	// -----------------

	public function __construct()
	{
		$file = require Internal::projectFile('config/registry.php');

		$this->entries = new Bucket($file);
	}

	// -----------------

	public function import(array $entries): void
	{
		$this->entries->import($entries);
	}

	public function entries(): Bucket
	{
		return $this->entries;
	}

	// -----------------

	public function has(string $key): bool
	{
		return $this->entries->has($key);
	}

	public function missing(string $key): bool
	{
		return $this->entries->missing($key);
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->entries->get($key, $default);
	}

	public function set(string $key, mixed $value = null): void
	{
		$this->entries->set($key, $value);
	}

	public function remove(string $key): void
	{
		$this->entries->remove($key);
	}

	// -----------------

	public function array(string $key, array $default = []): array
	{
		return $this->entries->array($key, $default);
	}

	public function bool(string $key, bool $default = false): bool
	{
		return $this->entries->bool($key, $default);
	}

	public function float(string $key, float $default = 0.00): float|false
	{
		return $this->entries->float($key, $default);
	}

	public function int(string $key, int $default = 0): int|false
	{
		return $this->entries->int($key, $default);
	}

	public function string(string $key, string $default = ''): string
	{
		return $this->entries->string($key, $default);
	}

	// -----------------

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		return $this->entries->date($key, $timezone);
	}

	public function enum(string $key, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return $this->entries->enum($key, $class, $default);
	}

	public function text(string $key, Text $default = new Text()): Text
	{
		return $this->entries->text($key, $default);
	}

	public function moment(string $key, mixed $default = null, DateTimeZone|int|string|null $timezone = null): Moment|null
	{
		return $this->entries->moment($key, $default, $timezone);
	}

}