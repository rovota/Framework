<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */


namespace Rovota\Framework\Http\Throttling;

use Closure;
use Rovota\Framework\Facades\Response;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Throttling\Enums\IdentifierType;
use Rovota\Framework\Http\Throttling\Traits\LimitStarters;
use Rovota\Framework\Support\Traits\Conditionable;

final class Limit
{
	use Conditionable, LimitStarters;

	// -----------------

	public LimitConfig $config {
		get {
			return $this->config;
		}
	}

	// -----------------

	public function __construct(int|null $limit = null)
	{
		$this->config = new LimitConfig();

		if ($limit !== null) {
			$this->config->limit = $limit;
		}
	}

	// -----------------

	public function by(string $identifier): Limit
	{
		$this->config->identifier_type = IdentifierType::Custom;
		$this->config->identifier = $identifier;
		return $this;
	}

	public function byIP(): Limit
	{
		$this->config->identifier_type = IdentifierType::IP;
		$this->config->identifier = RequestManager::instance()->current()->ip();
		return $this;
	}

	public function byToken(): Limit
	{
		$this->config->identifier_type = IdentifierType::Token;
		$this->config->identifier = RequestManager::instance()->current()->authToken() ?? '';
		return $this;
	}

	// -----------------

	public function response(Closure $callback): Limit
	{
		$this->config->response = $callback;
		return $this;
	}

	// -----------------

	public function hit(): void
	{
		$key = $this->getKeyName();

		if ($this->config->cache->has($key)) {
			$this->config->cache->increment($key);
		} else {
			$this->config->cache->set($key, 1, $this->config->period_in_seconds);
		}
	}

	public function reset(): void
	{
		$this->config->cache->remove($this->getKeyName());
	}

	public function attempts(): int
	{
		return $this->config->cache->get($this->getKeyName(), 0);
	}

	public function remaining(): int
	{
		return max($this->config->limit - $this->attempts(), 0);
	}

	public function tooManyAttempts(): bool
	{
		return $this->attempts() > $this->config->limit;
	}

	// -----------------

	/**
	 * @internal
	 */
	public function setResponseHeaders(): void
	{
		Response::attachHeaders([
			'X-RateLimit-Limit' => $this->config->limit,
			'X-RateLimit-Remaining' => $this->remaining(),
		]);
	}

	// -----------------

	protected function getKeyName(): string
	{
		return implode(':', ['limit', $this->config->identifier]);
	}

}