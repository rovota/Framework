<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Support\Clock;
use Rovota\Framework\Support\Media\QrCode;

final class OneTimePassword
{
	protected TOTPInterface $totp;

	// -----------------

	public function __construct(string|null $secret, int $digits = 6, int $period = 30, string $digest = 'sha1', int $timestamp = 0)
	{
		$clock = new Clock();
		$this->totp = $secret === null ? TOTP::generate($clock) : TOTP::createFromSecret($secret, $clock);
		$this->totp->setPeriod($period);
		$this->totp->setDigest($digest);
		$this->totp->setDigits($digits);
		$this->totp->setEpoch($timestamp);
	}

	// -----------------

	public static function generate(int $digits = 6, int $period = 30): self
	{
		return new self(null, $digits, $period);
	}

	public static function from(string $secret, int $digits = 6, int $period = 30): self
	{
		return new self($secret, $digits, $period);
	}

	// -----------------

	public function verify(string $input, int|null $timestamp = null, int|null $leeway = null): bool
	{
		$leeway = $leeway ?? round($this->totp->getPeriod() / 4);
		$result = $this->totp->verify($input, $timestamp, $leeway);

		if ($result === true) {
			$key = hash('sha256', $this->secret() . '-' . $input);
			if (CacheManager::instance()->get()->has($key)) {
				return false;
			}
			CacheManager::instance()->get()->set($key, 1, $this->totp->getPeriod());
		}

		return $result;
	}

	// -----------------

	public function current(): string
	{
		return $this->totp->now();
	}

	public function at(int $timestamp): string
	{
		return $this->totp->at($timestamp);
	}

	// -----------------

	public function label(string $label): self
	{
		$this->totp->setLabel(mb_trim($label));
		return $this;
	}

	public function issuer(string $issuer): self
	{
		$this->totp->setIssuer(mb_trim($issuer));
		return $this;
	}

	public function parameter(string $name, string $value): self
	{
		$this->totp->setParameter(trim($name), trim($value));
		return $this;
	}

	// -----------------

	public function secret(): string
	{
		return $this->totp->getSecret();
	}

	// -----------------

	public function image(): QrCode
	{
		return QrCode::from($this->totp->getProvisioningUri());
	}

}