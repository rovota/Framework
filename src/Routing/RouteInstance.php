<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Http\Controller;
use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;

final class RouteInstance extends RouteEntry
{
	use Conditionable;

	// -----------------

	protected RouteConfig $config;

	protected Bucket $attributes;

	// -----------------

	public function __construct(RouteEntry|null $parent = null)
	{
		parent::__construct($parent);

		$this->config = new RouteConfig();
	}

	// -----------------

	public function getConfig(): RouteConfig
	{
		return $this->config;
	}

	// -----------------

	public function getPath(): string
	{
		return $this->config->path;
	}

	public function getContext(): array
	{
		return $this->config->context;
	}

	public function getTarget(): mixed
	{
		return $this->config->getTarget();
	}

	public function getPattern(): string
	{
		return $this->buildPattern();
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

	public function path(string $path): RouteInstance
	{
		$path = trim($path, '/');

		if ($this->attributes->has('prefix')) {
			$path = implode('/', [$this->attributes->string('prefix'), $path]);
		}

		$this->config->path = $path;
		return $this;
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	/**
	 * @internal
	 */
	public function setContext(array $context): void
	{
		$this->config->context = $context;
	}

	/**
	 * @internal
	 */
	public function listensTo(RequestMethod $method): bool
	{
		return in_array($method->value, $this->config->methods);
	}

	// -----------------

	protected function buildPattern(): string
	{
		$path = $this->config->path;

		foreach ($this->attributes->array('parameters') as $name => $pattern) {
			$path = str_replace(sprintf('{%s}', $name), sprintf('(%s)', $pattern), $path);
		}

		$pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $path);
		return Str::start($pattern, '/');
	}

}