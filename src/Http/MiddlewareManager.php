<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Auth\Middleware\AttemptAuthentication;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Localization\Middleware\DetermineLanguage;
use Rovota\Framework\Security\Middleware\CsrfProtection;

/**
 * @internal
 */
final class MiddlewareManager extends ServiceProvider
{

	protected array $middleware = [];

	protected array $global = [];

	// -----------------

	public function __construct()
	{
		$this->registerDefaultMiddleware();
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->middleware[$name]);
	}

	public function add(string $name, string $target, bool $global = false): void
	{
		$this->middleware[$name] = $target;

		if ($global === true) {
			$this->global[] = $name;
		}
	}

	public function get(string $name): string
	{
		return $this->middleware[$name];
	}

	// -----------------

	public function setAsGlobal(array|string $names): void
	{
		foreach (is_array($names) ? $names : [$names] as $name) {
			if (isset($this->middleware[$name])) {
				$this->global[] = $name;
			}
		}
	}

	// -----------------

	public function execute(array $names, array $without = []): void
	{
		foreach ($this->global as $name) {
			if (!in_array($name, $without)) {
				Resolver::invoke([$this->middleware[$name], 'handle'], [RequestManager::instance()->current()]);
			}
		}

		foreach ($names as $name) {
			if (isset($this->middleware[$name]) && !in_array($name, $without)) {
				Resolver::invoke([$this->middleware[$name], 'handle'], [RequestManager::instance()->current()]);
			}
		}
	}

	// -----------------

	protected function registerDefaultMiddleware(): void
	{
		$this->add('auth', AttemptAuthentication::class, true);
		$this->add('determine_language', DetermineLanguage::class, true);
		$this->add('csrf', CsrfProtection::class, true);
	}

}