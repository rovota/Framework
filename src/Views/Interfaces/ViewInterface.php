<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Interfaces;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;

interface ViewInterface
{

	public function __toString(): string;

	// -----------------

	public function withLink(string $identifier, Link|array $data, bool $replace = false): static;

	public function withoutLink(array|string $identifiers): static;

	/**
	 * @return array<string, Link>
	 */
	public function getLinks(): array;

	// -----------------

	public function withMeta(string $identifier, Meta|array $data, bool $replace = false): static;

	public function withoutMeta(array|string $identifiers): static;

	/**
	 * @return array<string, Meta>
	 */
	public function getMeta(): array;

	// -----------------

	public function withScript(string $identifier, Script|array $data, bool $replace = false): static;

	public function withoutScript(array|string $identifiers): static;

	/**
	 * @return array<string, Script>
	 */
	public function getScripts(): array;

	// -----------------

	public function with(array|string $name, mixed $value = null): static;

	public function getVariables(): Bucket;

}