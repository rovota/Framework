<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Redis;
use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Support\Config;

class RedisAdapter implements CacheAdapterInterface
{

	protected Redis $redis;

	protected string|null $last_modified = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->redis = new Redis();
		$this->redis->connect($parameters->string('host', '127.0.0.1'));
		$this->redis->auth($parameters->get('password'));
		$this->redis->select($parameters->int('database', 2));
	}

	// -----------------

	public function all(): array
	{
		return [];
	}

	// -----------------

	public function has(string $key): bool
	{
		$result = $this->redis->exists($key);
		return $result === 1 || $result === true;
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified = $key;
		$this->redis->set($key, Resolver::serialize($value), $retention);
	}

	public function get(string $key): mixed
	{
		return $this->redis->exists($key) ? Resolver::deserialize($this->redis->get($key)) : null;
	}

	public function remove(string $key): void
	{
		$this->last_modified = $key;
		$this->redis->del($key);
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->redis->incrBy($key, max($step, 0));
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified = $key;
		$this->redis->decrBy($key, max($step, 0));
	}

	// -----------------

	public function flush(): void
	{
		$this->redis->flushDB();
	}

	// -----------------

	public function lastModified(): string|null
	{
		return $this->last_modified;
	}

}