<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Interfaces;

use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\DirectoryProperties;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Str;

interface DirectoryInterface
{

	public DirectoryProperties $properties {
		get;
	}

	public UrlObject $url {
		get;
	}

	// -----------------

	public function __toString(): string;

	// -----------------

	public function contents(): Sequence;

	public function files(): Sequence;

	public function directories(): Sequence;

	// -----------------

	public function exists(string $location): bool;

	public function missing(string $location): bool;

	// -----------------

	public function checksum(string $location, array $config = []): string;

	// -----------------

	public function compress(string|null $target = null): FileInterface|null;

	// -----------------

	public function move(string $to): bool;

	public function rename(string $name): bool;

	public function copy(string $to): bool;

	// -----------------

	public function delete(): bool;

	public function clear(): bool;

	// -----------------

	public function location(): string;

}