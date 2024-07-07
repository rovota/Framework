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

	public function jsonSerialize(): string
	{
		return '---';
	}

}