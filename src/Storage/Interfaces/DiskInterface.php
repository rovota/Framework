<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Interfaces;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\DiskConfig;
use Rovota\Framework\Structures\Sequence;
use Rovota\Framework\Support\Moment;

interface DiskInterface
{

	public string $name {
		get;
	}

	public DiskConfig $config {
		get;
	}

	public FilesystemAdapter $adapter {
		get;
	}

	public Filesystem $flysystem {
		get;
	}

	public UrlObject $url {
		get;
	}

	// -----------------

	public function __toString(): string;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function asHash(string $location, string $algo = 'sha256', bool $binary = false): string|null;

	public function asString(string $location): string;

	// -----------------

	public function contents(string $location = '/'): Sequence;

	public function files(string $location = '/'): Sequence;

	public function directories(string $location = '/'): Sequence;

	// -----------------

	public function exists(string $location): bool;

	public function missing(string $location): bool;

	// -----------------

	public function checksum(string $location, array $config = []): string;

	// -----------------

	public function file(string $location, array $without = []): FileInterface|null;

	public function directory(string $location): DirectoryInterface|null;

	// -----------------

	public function read(string $location): string|null;

	public function write(string $location, string $contents): bool;

	public function writeStream(string $location, mixed $contents): bool;

	// -----------------

	public function move(string $from, string $to): bool;

	public function rename(string $location, string $name): bool;

	public function copy(string $from, string $to): bool;

	// -----------------

	public function delete(string $location): bool;

	public function deleteDirectory(string $location): bool;

	public function clear(string $location): bool;

	public function clearDirectory(string $location): bool;

	// -----------------

	public function lastModified(string $location): Moment|null;

	public function size(string $location): int;

	public function mimeType(string $location): string|null;

	// -----------------

	public function compress(string $source, string|null $target = null): FileInterface|null;

	public function extract(string $source, string|null $target = null): DirectoryInterface|null;

	// -----------------

	public function prepend(string $location, string $contents, bool $new_line = true): void;

	public function append(string $location, string $contents, bool $new_line = true): void;

	// -----------------

	public function isExtension(string $location, string $extension): bool;

	public function isAnyExtension(string $location, array $extensions): bool;

	// -----------------

	public function isMimeType(string $location, string $mime_type): bool;

	public function isAnyMimeType(string $location, array $mime_types): bool;

	// -----------------

	public function isEqual(string $first, string $second): bool;

}