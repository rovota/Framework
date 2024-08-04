<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use DateTime;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Security\Encryption;
use Rovota\Framework\Support\Moment;
use Throwable;

final class Cookie
{

	protected string $name;

	protected string $value;

	protected array $options;

	protected bool $received;

	// -----------------

	public function __construct(string $name, string $value, array $options = [], bool $received = false)
	{
		$this->name = $name;
		$this->value = $value;
		$this->received = $received;

		$this->setDefaultOptions();
		$this->setOptions($options);
	}

	public function __toString(): string
	{
		return $this->value;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function prefixedName(): string
	{
		return $this->addPrefix($this->name);
	}

	public function value(): string
	{
		return $this->value;
	}

	// -----------------

	public function apply(): bool
	{
		$name = $this->options['secure'] ? $this->prefixedName() : $this->name();
		$value = trim($this->value);

		try {
			if (CookieManager::hasEncryptionEnabled($this->name)) {
				$value = Encryption::encryptString($value);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return false;
		}

		return setcookie($name, $value, $this->options);
	}

	public function update(string|null $value, array $options = []): void
	{
		if ($value !== null) {
			$this->value = $value;
		}
		$this->setOptions($options);
	}

	public function expire(): bool
	{
		$this->setOptions([
			'expires' => -1,
		]);
		return $this->apply();
	}

	// -----------------

	protected function addPrefix(string $name): string
	{
		return sprintf('__Secure-%s', $name);
	}

	protected function stripPrefix(string $name): string
	{
		return str_replace('__Secure-', '', trim($name));
	}

	// -----------------

	protected function setDefaultOptions(): void
	{
		$this->options = [
			'domain' => Framework::environment()->cookieDomain(),
			'expires' => 0, // when client is closed
			'path' => '/',
			'httponly' => true,
			'secure' => true,
			'samesite' => 'Lax'
		];
	}

	protected function setOptions(array $options): void
	{
		foreach ($options as $key => $value) {
			if ($key === 'expires') {
				$value = ceil(match (true) {
					$value instanceof Moment => $value->toEpochString(),
					$value instanceof DateTime => (int)$value->format('U'),
					default => time() + ($value * 60),
				});
			}

			if (isset($this->options[$key])) {
				$this->options[$key] = $value;
			}
		}
	}

}