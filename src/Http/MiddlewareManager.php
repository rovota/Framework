<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Kernel\ServiceProvider;

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

	public function hasMiddleware(string $name): bool
	{
		return isset($this->middleware[$name]);
	}

	public function addMiddleware(string $name, string $target, bool $global = false): void
	{
		$this->middleware[$name] = $target;

		if ($global === true) {
			$this->global[] = $name;
		}
	}

	public function getMiddleware(string $name): array
	{
		return $this->middleware;
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
				Resolver::invoke($this->middleware[$name], [RequestManager::instance()->getCurrent()]);
			}
		}

		foreach ($names as $name) {
			if (isset($this->middleware[$name]) && !in_array($name, $without)) {
				Resolver::invoke($this->middleware[$name], [RequestManager::instance()->getCurrent()]);
			}
		}
	}

	// -----------------

	protected function registerDefaultMiddleware(): void
	{
		// TODO: AttemptAuthentication
		// TODO: DetermineLanguage
		// TODO: CsrfProtection
	}

}