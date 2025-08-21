<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Kernel\Exceptions\UnsupportedDriverException;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Mail\Components\Entity;
use Rovota\Framework\Mail\Drivers\Smtp;
use Rovota\Framework\Mail\Enums\Driver;
use Rovota\Framework\Mail\Exceptions\MissingMailerException;
use Rovota\Framework\Mail\Exceptions\MailerMisconfigurationException;
use Rovota\Framework\Mail\Interfaces\MailerInterface;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class MailManager extends ServiceProvider
{

	/**
	 * @var Map<string, MailerInterface>
	 */
	protected Map $mailers;

	protected string $default;

	// -----------------

	public function __construct()
	{
		$this->mailers = new Map();

		$file = MailConfig::load('config/mail');

		foreach ($file->mailers as $name => $config) {
			$mailer = $this->build($name, $config);
			if ($mailer instanceof MailerInterface) {
				$this->mailers->set($name, $mailer);
			}
		}

		if (count($file->mailers) > 0) {
			$this->setDefault($file->default);
		}
	}

	// -----------------

	public function createMailer(array $config, string|null $name = null): MailerInterface|null
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
		$store = $this->build($name, $config);

		if ($store instanceof MailerInterface) {
			$this->mailers[$name] = $store;
		}
	}

	public function get(string|null $name = null): MailerInterface
	{
		if ($name === null) {
			$name = $this->default;
		}

		if (isset($this->mailers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingMailerException("The specified mailer could not be found: '$name'."));
		}

		return $this->mailers[$name];
	}

	public function getWithDriver(Driver $driver): MailerInterface|null
	{
		return $this->mailers->first(function (MailerInterface $store) use ($driver) {
			return $store->config->driver === $driver;
		});
	}

	// -----------------

	/**
	 * @returns Map<string, MailerInterface>
	 */
	public function all(): Map
	{
		return $this->mailers;
	}

	// -----------------

	public function setDefault(string $name): void
	{
		if (isset($this->mailers[$name]) === false) {
			ExceptionHandler::handleThrowable(new MissingMailerException("Undefined mailers cannot be set as default: '$name'."));
		}
		$this->default = $name;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	// -----------------

	protected function build(string $name, array $config): MailerInterface|null
	{
		$config = new MailerConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::handleThrowable(new UnsupportedDriverException($config->get('driver')));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::handleThrowable(new MailerMisconfigurationException("The cache '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match ($config->driver) {
			Driver::SMTP => new Smtp($name, $config),
//			Driver::Basic => new Basic($name, $config),
			default => null,
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