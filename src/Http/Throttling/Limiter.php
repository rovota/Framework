<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Throttling;

use Closure;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Kernel\Resolver;
use Rovota\Framework\Structures\Bucket;

final class Limiter
{
	/**
	 * @var array<string, Limit>
	 */
	public array $limits = [];

	// -----------------

	public function __construct(string $name, Closure|Limit $limits)
	{
		if ($limits instanceof Limit) {
			$this->limits[$name] = $limits;
			return;
		}

		$limits = $limits(RequestManager::instance()->current());
		$limits = $limits instanceof Limit ? [$limits] : $limits;

		foreach ($limits as $number => $limit) {
			$this->limits[$name . '-' . $number] = $limit;
		}
	}

	// -----------------

	public function hit(): void
	{
		foreach ($this->limits as $limit) {
			$limit->hit();
		}
	}

	public function reset(): void
	{
		foreach ($this->limits as $limit) {
			$limit->reset();
		}
	}

	public function attempts(): Bucket
	{
		$attempts = new Bucket();
		foreach ($this->limits as $name => $limit) {
			$attempts->set($name, $limit->attempts());
		}

		return $attempts;
	}

	public function remaining(): Bucket
	{
		$remaining = new Bucket();
		foreach ($this->limits as $name => $limit) {
			$remaining->set($name, $limit->remaining());
		}

		return $remaining;
	}

	public function tooManyAttempts(): bool
	{
		return array_any($this->limits, fn($limit) => $limit->tooManyAttempts());
	}

	// -----------------

	/**
	 * @internal
	 */
	public function hitAndTry(): void
	{
		foreach ($this->limits as $limit) {
			$limit->hit();
			$limit->setResponseHeaders();

			if ($limit->tooManyAttempts()) {
				$response = Resolver::invoke($limit->config->response);
				echo ($response instanceof DefaultResponse) ? $response : response($response);
				exit;
			}
		}
	}

}