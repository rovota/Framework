<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Interfaces;

use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\FileProperties;

interface FileInterface
{

	public string|null $contents {
		get;
	}

	public FileProperties $properties {
		get;
	}

	public UrlObject $url {
		get;
	}

	// -----------------

	public function __toString(): string;

	// -----------------

	public function write(mixed $content): static;

	// -----------------

	public function compress(string|null $target = null): FileInterface|null;

	public function extract(string|null $target = null): DirectoryInterface|null;

	// -----------------

	public function move(string $to): bool;

	public function rename(string $name): bool;

	public function copy(string $to): bool;

	// -----------------

	public function delete(): bool;

	public function clear(): static;

	// -----------------

	public function prepend(string $content, bool $new_line = true): static;

	public function append(string $content, bool $new_line = true): static;

	public function findAndReplace(array|string $search, array|string $replace, bool $count = false): static|int;

	// -----------------

	public function isExtension(string $extension): bool;

	public function isAnyExtension(array $extensions): bool;

	// -----------------

	public function isMimeType(string $mime_type): bool;

	public function isAnyMimeType(array $mime_types): bool;

	// -----------------

	public function save(): static;

	// -----------------

	public function location(): string;

}