<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Config;

class SessionAdapter implements CacheAdapterInterface
{

	protected string|null $last_modified = null;

	protected string|null $scope = null;

	protected bool $session_loaded;

	protected string $cookie_name;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->session_loaded = false;
		$this->cookie_name = $parameters->string('cookie_name', 'session');

		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		$this->initialize();
		return $_SESSION;
	}

	// -----------------

	public function has(string $key): bool
	{
		$this->initialize();
		return array_key_exists($this->getScopedKey($key), $_SESSION);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->initialize();
		$this->last_modified = $key;
		$_SESSION[$this->getScopedKey($key)] = $value;
	}

	public function get(string $key): mixed
	{
		$this->initialize();
		return $_SESSION[$this->getScopedKey($key)];
	}

	public function remove(string $key): void
	{
		$this->initialize();
		$this->last_modified = $key;
		unset($_SESSION[$this->getScopedKey($key)]);
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->initialize();
		$this->last_modified = $key;
		$_SESSION[$this->getScopedKey($key)] = $_SESSION[$this->getScopedKey($key)] + max($step, 0);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->initialize();
		$this->last_modified = $key;
		$_SESSION[$this->getScopedKey($key)] = $_SESSION[$this->getScopedKey($key)] - max($step, 0);
	}

	// -----------------

	public function flush(): void
	{
		$this->initialize();
		$_SESSION = [];
	}

	// -----------------

	public function lastModified(): string|null
	{
		return $this->last_modified;
	}

	// -----------------

	protected function getScopedKey(string $key): string
	{
		if ($this->scope === null || mb_strlen($this->scope) === 0) {
			return $key;
		}
		return sprintf('%s:%s', $this->scope, $key);
	}

	protected function initialize(): void
	{
		if ($this->session_loaded === false) {
			session_set_cookie_params([
				'lifetime' => 0,
				'path' => '/',
				'domain' => Framework::environment()->config()->cookie_domain,
				'httponly' => true,
				'secure' => true,
				'samesite' => 'Lax',
			]);

			session_name('__Secure-'.$this->cookie_name);
			session_start();
		}
	}

}