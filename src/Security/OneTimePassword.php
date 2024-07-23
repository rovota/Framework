<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use Rovota\Framework\Support\Clock;
use Rovota\Framework\Support\QrCode;

final class OneTimePassword
{
	protected TOTPInterface $agent;

	// -----------------

	public function __construct(string|null $secret, int $digits = 6, int $period = 30, string $digest = 'sha1', int $timestamp = 0)
	{
		$clock = new Clock();
		$this->agent = $secret === null ? TOTP::generate($clock) : TOTP::createFromSecret($secret, $clock);
		$this->agent->setPeriod($period);
		$this->agent->setDigest($digest);
		$this->agent->setDigits($digits);
		$this->agent->setEpoch($timestamp);
	}

	// -----------------

	public function agent(): TOTPInterface
	{
		return $this->agent;
	}

	// -----------------

	public static function generate(int $digits = 6, int $period = 30): self
	{
		return new self(null, $digits, $period);
	}

	public static function fromSecret(string $secret, int $digits = 6, int $period = 30): self
	{
		return new self($secret, $digits, $period);
	}

	// -----------------

	public function verify(string $input, int|null $timestamp = null, int|null $leeway = null): bool
	{
		$leeway = $leeway ?? round($this->agent->getPeriod() / 4);
		$result = $this->agent->verify($input, $timestamp, $leeway);

		// TODO: Cache the result, so that successfully validated OTPs cannot be used again.

		return $result;
	}

	// -----------------

	public function current(): string
	{
		return $this->agent->now();
	}

	public function at(int $timestamp): string
	{
		return $this->agent->at($timestamp);
	}

	// -----------------

	public function label(string $label): self
	{
		$this->agent->setLabel(trim($label));
		return $this;
	}

	public function issuer(string $issuer): self
	{
		$this->agent->setIssuer(trim($issuer));
		return $this;
	}

	public function parameter(string $name, string $value): self
	{
		$this->agent->setParameter(trim($name), trim($value));
		return $this;
	}

	// -----------------

	public function secret(): string
	{
		return $this->agent->getSecret();
	}

	// -----------------

	public function image(): QrCode
	{
		return QrCode::from($this->agent()->getProvisioningUri());
	}

}