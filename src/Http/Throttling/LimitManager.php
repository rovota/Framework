<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling;

use Closure;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Routing\RouteManager;

/**
 * @internal
 */
final class LimitManager extends ServiceProvider
{
	/**
	 * @var array<string, Limiter>
	 */
	protected array $limiters = [];

	// -----------------

	public function __construct()
	{
		$this->defineDefaultLimiters();
	}

	// -----------------

	public function define(string $name, Closure|Limit $callback): Limiter
	{
		$this->limiters[$name] = new Limiter($name, $callback);
		return $this->limiters[$name];
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->limiters[$name]);
	}

	public function get(string $name): Limiter|null
	{
		return $this->limiters[$name];
	}

	public function getActiveLimiter(): Limiter|null
	{
		$route = RouteManager::instance()->router->getCurrentRoute();

		if ($route === null) {
			return null;
		}

		return $this->limiters[$route->attributes->string('limiter')];
	}

	// -----------------

	/**
	 * @returns array<string, Limiter>
	 */
	public function all(): array
	{
		return $this->limiters;
	}

	// -----------------

	protected function defineDefaultLimiters(): void
	{
		$this->define('default', function () {
			return Limit::perMinute(200)->byIP();
		});
	}

}