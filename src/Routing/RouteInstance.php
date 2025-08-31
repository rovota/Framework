<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Auth\AuthManager;
use Rovota\Framework\Auth\Provider;
use Rovota\Framework\Http\Controller;
use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;

final class RouteInstance extends RouteEntry
{
	use Conditionable;

	// -----------------

	public function __construct(RouteEntry|null $parent = null)
	{
		parent::__construct($parent);
	}

	// -----------------

	public function methods(array|string $methods): RouteInstance
	{
		if (is_string($methods)) {
			$methods = explode('|', $methods);
		}

		$this->config->methods = $methods;
		return $this;
	}

	public function target(mixed $target): RouteInstance
	{
		if (is_string($target)) {
			$target = [$this->attributes->string('controller', Controller::class), $target];
		}

		$this->config->target = $target;
		return $this;
	}

	// -----------------

	/**
	 * @internal
	 */
	public function listensTo(RequestMethod $method): bool
	{
		return in_array($method->value, $this->config->methods);
	}

	/**
	 * @internal
	 */
	public function getPattern(): string
	{
		return $this->buildPattern();
	}

	public function getAuthProvider(): Provider|null
	{
		if ($this->attributes->get('auth')) {
			return AuthManager::instance()->get($this->attributes->get('auth'));
		}

		return AuthManager::instance()->all()->count() > 0 ? AuthManager::instance()->get() : null;
	}

	// -----------------

	protected function buildPattern(): string
	{
		$path = $this->config->path;

		foreach ($this->attributes->array('parameters') as $name => $pattern) {
			$path = str_replace(sprintf('{%s}', $name), sprintf('(%s)', $pattern), $path);
		}

		$pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $path);
		return Str::start(Str::trimEnd($pattern, '/'), '/');
	}

}