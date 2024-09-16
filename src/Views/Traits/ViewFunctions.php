<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Traits;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;

trait ViewFunctions
{

	public function withLink(string $identifier, Link|array $data, bool $replace = false): static
	{
		if ($replace === true && $this->config->has('links.' . $identifier)) {
			foreach ($data as $key => $value) {
				$this->config->get('links.' . $identifier)->withAttribute($key, $value);
			}
			return $this;
		}

		$this->config->set('links.' . $identifier, $data instanceof Link ? $data : new Link($data));

		return $this;
	}

	public function withoutLink(array|string $identifiers): static
	{
		$this->removeForType('links', $identifiers);
		return $this;
	}

	/**
	 * @return array<string, Link>
	 */
	public function getLinks(): array
	{
		return $this->config->links;
	}

	// -----------------

	public function withMeta(string $identifier, Meta|array $data, bool $replace = false): static
	{
		if ($replace === true && $this->config->has('meta.' . $identifier)) {
			foreach ($data as $key => $value) {
				$this->config->get('meta.' . $identifier)->withAttribute($key, $value);
			}
			return $this;
		}

		$this->config->set('meta.' . $identifier, $data instanceof Meta ? $data : new Meta($data));

		return $this;
	}

	public function withoutMeta(array|string $identifiers): static
	{
		$this->removeForType('meta', $identifiers);
		return $this;
	}

	/**
	 * @return array<string, Meta>
	 */
	public function getMeta(): array
	{
		return $this->config->meta;
	}

	// -----------------

	public function withScript(string $identifier, Script|array $data, bool $replace = false): static
	{
		if ($replace === true && $this->config->has('scripts.' . $identifier)) {
			foreach ($data as $key => $value) {
				$this->config->get('scripts.' . $identifier)->withAttribute($key, $value);
			}
			return $this;
		}

		$this->config->set('scripts.' . $identifier, $data instanceof Script ? $data : new Script($data));

		return $this;
	}

	public function withoutScript(array|string $identifiers): static
	{
		$this->removeForType('scripts', $identifiers);
		return $this;
	}

	/**
	 * @return array<string, Script>
	 */
	public function getScripts(): array
	{
		return $this->config->scripts;
	}

	// -----------------

	public function with(array|string $name, mixed $value = null): static
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$this->with($key, $value);
			}

			return $this;
		}

		$this->config->set('variables.' . $name, $value);

		return $this;
	}

	public function getVariables(): Bucket
	{
		return Bucket::from($this->config->variables);
	}

	// -----------------

	protected function removeForType(string $type, array|string $identifiers): static
	{
		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];

		if (empty($identifiers)) {
			$this->config->remove($type);
		} else {
			foreach ($identifiers as $identifier) {
				$this->config->remove($type . '.' . $identifier);
			}
		}
		return $this;
	}

}