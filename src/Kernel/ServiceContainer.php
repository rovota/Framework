<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class ServiceContainer
{

	protected Bucket $services;

	// -----------------

	public function __construct()
	{
		$this->services = new Bucket();
	}

	// -----------------

	public function register(array|string $class, string|null $name = null): void
	{
		if (is_array($class)) {
			foreach ($class as $name => $value) {
				$this->register($value, is_string($name) ? $name : null);
			}
			return;
		}

		$this->services[$name ?? Str::random(20)] = new $class;
	}

	// -----------------

	public function all(): Bucket
	{
		return $this->services;
	}

	// -----------------

	public function get(string $name): object|null
	{
		return $this->services->get($name);
	}

	public function resolve(string $class): object|null
	{
		return $this->services->first(function (object $service) use ($class) {
			return $service::class === $class;
		});
	}

}