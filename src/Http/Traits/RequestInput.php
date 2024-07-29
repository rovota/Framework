<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Traits;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Framework\Http\RequestData;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Text;

trait RequestInput
{

	protected readonly string|null $body;

	protected readonly RequestData $post;
	protected readonly RequestData $query;

	// -----------------

	public function body(): string|null
	{
		return $this->body;
	}

	// -----------------

	public function post(): RequestData
	{
		return $this->post;
	}

	public function query(): RequestData
	{
		return $this->query;
	}

	// -----------------

	public function all(): array
	{
		return array_merge($this->post->all(), $this->query->all());
	}

	public function has(string|array $key): bool
	{
		return $this->query->has($key) || $this->post->has($key);
	}

	public function filled(string|array $key): bool
	{
		return $this->query->filled($key) || $this->post->filled($key);
	}

	public function missing(string|array $key): bool
	{
		return $this->query->missing($key) && $this->post->missing($key);
	}

	public function pull(string $key, mixed $default = null): mixed
	{
		$fallback = $this->post->pull($key) ?? $default;
		return $this->query->pull($key, $fallback);
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->query->get($key) ?? $this->post->get($key) ?? $default;
	}

	public function remove(string $key): static
	{
		$this->query->remove($key);
		$this->post->remove($key);
		return $this;
	}

	// -----------------

	public function array(string $key, array $default = []): array
	{
		return $this->query->array($key, $this->post->array($key, $default));
	}

	public function bool(string $key, bool $default = false): bool
	{
		return $this->query->bool($key, $this->post->bool($key, $default));
	}

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		return $this->query->date($key, $timezone) ?? $this->post->date($key, $timezone);
	}

	public function enum(string $key, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return $this->query->enum($key, $class, $this->post->enum($key, $class, $default));
	}

	public function float(string $key, float $default = 0.00): float
	{
		return $this->query->float($key, $this->post->float($key, $default));
	}

	public function int(string $key, int $default = 0): int
	{
		return $this->query->int($key, $this->post->int($key, $default));
	}

	public function string(string $key, string $default = ''): string
	{
		return $this->query->string($key, $this->post->string($key, $default));
	}

	// -----------------

	public function text(string $key, Text|string $default = new Text()): Text
	{
		return $this->query->text($key, $this->post->text($key, $default));
	}

	public function moment(string $key, mixed $default = null, DateTimeZone|int|string|null $timezone = null): Moment|null
	{
		return $this->query->moment($key, $this->post->moment($key, $default, $timezone), $timezone);
	}

	// -----------------

	public function json(): string|null
	{
		if (json_validate($this->body ?? '') !== null) {
			return $this->body;
		}
		return null;
	}

	public function jsonAsArray(): array
	{
		$json = json_decode($this->body ?? '', true);
		if ($json !== null) {
			return is_array($json) ? $json : [$json];
		}
		return [];
	}

}