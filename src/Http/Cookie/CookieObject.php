<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Cookie;

use DateTime;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Security\EncryptionManager;
use Rovota\Framework\Support\Moment;
use Throwable;

final class CookieObject
{

	public string $name;

	public string $value;

	protected array $options;

	protected bool $received;

	// -----------------

	public function __construct(string $name, string $value, array $options = [], bool $received = false)
	{
		$this->setDefaultOptions();

		$this->name = $name;
		$this->value = $value;
		$this->received = $received;
		$this->update($options);
	}

	public function __toString(): string
	{
		return $this->value;
	}

	// -----------------

	public static function create(string $name, string $value, array $options = [], bool $received = false): CookieObject
	{
		return new CookieObject($name, $value, $options, $received);
	}

	// -----------------

	public function prefixedName(): string
	{
		return $this->addPrefix($this->name);
	}

	// -----------------

	public function apply(): bool
	{
		$name = $this->options['secure'] ? $this->prefixedName() : $this->name;
		$value = mb_trim($this->value);

		try {
			if (CookieManager::instance()->hasEncryptionEnabled($this->name)) {
				$value = EncryptionManager::instance()->agent->encrypt($value, false);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return false;
		}

		return setcookie($name, $value, $this->options);
	}

	public function expire(): bool
	{
		$this->update([
			'expires' => -1,
		]);
		return $this->apply();
	}

	public function update(array $options): void
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

	// -----------------

	protected function addPrefix(string $name): string
	{
		return sprintf('__Secure-%s', $name);
	}

	// -----------------

	protected function setDefaultOptions(): void
	{
		$this->options = [
			'domain' => Framework::environment()->config->cookie_domain,
			'expires' => 0, // when client is closed
			'path' => '/',
			'httponly' => true,
			'secure' => true,
			'samesite' => 'Lax'
		];
	}

}