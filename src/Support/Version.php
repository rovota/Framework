<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support;

use JsonSerializable;
use PHLAK\SemVer\Exceptions\InvalidVersionException;
use PHLAK\SemVer\Version as SemVer;

final class Version implements JsonSerializable
{

	protected SemVer $version;

	// -----------------

	/**
	 * @throws InvalidVersionException
	 */
	public function __construct(string $version)
	{
		$this->version = new SemVer(trim($version));
	}

	// -----------------

	public function __toString(): string
	{
		return $this->full();
	}

	public function jsonSerialize(): string
	{
		return $this->full();
	}

	// -----------------

	public function setMajor(int $value): Version
	{
		$this->version->setMajor($value);
		return $this;
	}

	public function setMinor(int $value): Version
	{
		$this->version->setMinor($value);
		return $this;
	}

	public function setPatch(int $value): Version
	{
		$this->version->setPatch($value);
		return $this;
	}

	public function setPreRelease(string|null $value): Version
	{
		$this->version->setPreRelease($value);
		return $this;
	}

	public function setBuild(string|null $value): Version
	{
		$this->version->setBuild($value);
		return $this;
	}

	// -----------------

	public function incrementMajor(): Version
	{
		$this->version->incrementMajor();
		return $this;
	}

	public function incrementMinor(): Version
	{
		$this->version->incrementMinor();
		return $this;
	}

	public function incrementPatch(): Version
	{
		$this->version->incrementPatch();
		return $this;
	}

	// -----------------

	public function basic(): string
	{
		$version = implode('.', [$this->version->major, $this->version->minor, $this->version->patch]);
		if (empty($this->preRelease) === false) {
			$version .= '-'.$this->preRelease;
		}
		return $version;
	}

	public function full(): string
	{
		$version = $this->basic();
		if (empty($this->build) === false) {
			$version .= '+'.$this->build;
		}
		return $version;
	}

	public function format(string $format): string
	{
		$matches = [];
		if (preg_match_all('#\{([a-z\d_]*)}#m', $format, $matches) > 0) {
			foreach ($matches[1] as $element) {
				if (method_exists($this, $element)) {
					$format = str_replace('{'.$element.'}', $this->{$element}(), $format);
				}
			}
		}
		return $format;
	}

	// -----------------

	public function major(): int
	{
		return $this->version->major;
	}

	public function minor(): int
	{
		return $this->version->minor;
	}

	public function patch(): int
	{
		return $this->version->patch;
	}

	public function preRelease(): string|null
	{
		return $this->version->preRelease;
	}

	public function build(): string|null
	{
		return $this->version->build;
	}

	// -----------------

	public function isGreater(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->gt($version->semVer());
	}

	public function isLower(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->lt($version->semVer());
	}

	public function isEqual(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->eq($version->semVer());
	}

	public function isNotEqual(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->neq($version->semVer());
	}

	public function isEqualOrGreater(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->gte($version->semVer());
	}

	public function isEqualOrLower(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->version->lte($version->semVer());
	}

	// -----------------

	public function semVer(): SemVer
	{
		return $this->version;
	}

	protected function getInstance(Version|string $version): Version|null
	{
		try {
			$version = is_string($version) ? new Version($version) : $version;
		} catch(InvalidVersionException) {
			return null;
		}
		return $version;
	}

}