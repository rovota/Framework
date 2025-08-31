<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\MisconfiguredServiceException;
use Rovota\Framework\Kernel\Exceptions\MissingInstanceException;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Mail\Components\Entity;
use Rovota\Framework\Mail\Drivers\Smtp;
use Rovota\Framework\Mail\Enums\Driver;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class MailManager extends ServiceProvider
{

	/**
	 * @var Map<string, Mailer>
	 */
	protected Map $mailers;

	public readonly string $default;

	// -----------------

	public function __construct()
	{
		$this->mailers = new Map();

		$file = MailConfig::load('config/mail');

		foreach ($file->mailers as $name => $config) {
			$this->mailers->set($name, $this->build($name, $config));
		}

		if (count($file->mailers) > 0 && isset($this->mailers[$file->default])) {
			$this->default = $this->mailers[$file->default];
		}
	}

	// -----------------

	public function createMailer(array $config, string|null $name = null): Mailer
	{
		return $this->build($name ?? Str::random(20), $config);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->mailers[$name]);
	}

	public function add(string $name, array $config): void
	{
		$this->mailers[$name] = $this->build($name, $config);
	}

	public function get(string|null $name = null): Mailer
	{
		if ($name === null && property_exists($this, 'default')) {
			$name = $this->default;
		}

		if (isset($this->mailers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingInstanceException("The specified mailer could not be found: '$name'."));
		}

		return $this->mailers[$name];
	}

	// -----------------

	/**
	 * @returns Map<string, Mailer>
	 */
	public function all(): Map
	{
		return $this->mailers;
	}

	// -----------------

	protected function build(string $name, array $config): Mailer
	{
		$config = new MailerConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			exit;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MisconfiguredServiceException("The cache '$name' cannot be used due to a configuration issue."));
			exit;
		}

		return match ($config->driver) {
			Driver::SMTP => new Smtp($name, $config),
		};
	}

	// -----------------

	public static function getNormalizedEntity(mixed $address, string|null $name = null): Entity
	{
		if ($address instanceof Entity) {
			return $address;
		}

		if ($address instanceof User) {
			return new Entity($address->nickname, $address->email);
		}

		if (is_array($address)) {
			return new Entity(...$address);
		}

		return new Entity($name ?? 'Unknown', $address);
	}

}