<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Caching\Adapters;

use Rovota\Framework\Caching\Interfaces\CacheAdapterInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Path;

class SessionAdapter implements CacheAdapterInterface
{

	protected string|null $last_modified = null;

	protected string|null $scope = null;

	protected string $cookie_name;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->cookie_name = $parameters->string('cookie_name', 'session');

		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		$this->initIfCookiePresent();
		return $_SESSION ?? [];
	}

	// -----------------

	public function has(string $key): bool
	{
		$this->initIfCookiePresent();
		return array_key_exists($this->getScopedKey($key), $_SESSION ?? []);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->initialize();
		$this->last_modified = $key;
		if (isset($_SESSION)) {
			$_SESSION[$this->getScopedKey($key)] = $value;
		}
	}

	public function get(string $key): mixed
	{
		$this->initIfCookiePresent();
		return $_SESSION[$this->getScopedKey($key)] ?? null;
	}

	public function remove(string $key): void
	{
		$this->initIfCookiePresent();
		$this->last_modified = $key;
		unset($_SESSION[$this->getScopedKey($key)]);
	}

	// -----------------

	public function increment(string $key, int $step = 1): void
	{
		$this->initIfCookiePresent();
		$this->last_modified = $key;
		if (isset($_SESSION)) {
			$_SESSION[$this->getScopedKey($key)] = ($_SESSION[$this->getScopedKey($key)] ?? 0) + max($step, 0);
		}
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->initIfCookiePresent();
		$this->last_modified = $key;
		if (isset($_SESSION)) {
			$_SESSION[$this->getScopedKey($key)] = ($_SESSION[$this->getScopedKey($key)] ?? 0) - max($step, 0);
		}
	}

	// -----------------

	public function flush(): void
	{
		$this->initIfCookiePresent();
		if (isset($_SESSION)) {
			$_SESSION = [];
		}
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

	// -----------------

	protected function initIfCookiePresent(): void
	{
		if (isset($_COOKIE['__Secure-'.$this->cookie_name])) {
			$this->initialize();
		}
	}

	protected function initialize(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {

			session_save_path(Path::toProjectFile('/storage/runtime/sessions'));

			session_set_cookie_params([
				'lifetime' => 0,
				'path' => '/',
				'domain' => Framework::environment()->config()->cookie_domain,
				'httponly' => true,
				'secure' => true,
				'samesite' => 'Lax',
			]);

			if ($this->createSessionDirectoryIfMissing()) {
				set_error_handler(function(int $number, string $message, string $file, int $line) {
					SessionAdapter::handleSessionError($number, $message, $file, $line);
				});

				session_name('__Secure-'.$this->cookie_name);
				session_start();

				restore_error_handler();
			}
		}
	}

	// -----------------

	protected function createSessionDirectoryIfMissing(): bool
	{
		$directory = Path::toProjectFile('storage/runtime/sessions');

		if (!file_exists($directory)) {
			return mkdir($directory, 0777, true);
		}

		return true;
	}

	// -----------------

	public static function handleSessionError(int $number, string $message, string $file, int $line): void
	{
		ExceptionHandler::logError($number, $message, $file, $line);
		session_regenerate_id(true);
		$_SESSION = [];
	}

}