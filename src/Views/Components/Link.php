<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Components;

use Rovota\Framework\Support\Str;

final class Link extends Asset
{

	public function version(string $value): Link
	{
		$this->config->set('version', trim($value));
		return $this;
	}

	// -----------------

	public function source(string $url): Link
	{
		$this->setAttribute('href', trim($url));
		return $this;
	}

	public function font(string $url): Link
	{
		if (Str::endsWithAny($url, ['.ttf', '.woff2'])) {
			$this->setAttribute('as', Str::afterLast($url, '.'));
		}

		$this->setAttribute('href', $url);
		return $this;
	}

	public function style(string $url): Link
	{
		if (str_ends_with($url, '.css')) {
			$this->setAttribute('rel', 'stylesheet');
		}

		$this->setAttribute('href', $url);
		return $this;
	}

	public function icon(string $url): Link
	{
		if (str_ends_with($url, '.ico')) {
			$this->setAttribute('rel', 'icon');
		}

		if (str_ends_with($url, '.png')) {
			$this->setAttribute('rel', 'apple-touch-icon');
		}

		$this->setAttribute('href', $url);
		return $this;
	}

	// -----------------

	public function media(string $value): Link
	{
		$this->setAttribute('media', trim($value));
		return $this;
	}

	public function type(string $value): Link
	{
		$this->setAttribute('type', trim($value));
		return $this;
	}

	public function sizes(array|null $values): Link
	{
		if (is_array($values)) {
			$this->setAttribute('sizes', implode(' ', $values));
			return $this;
		}

		$this->setAttribute('sizes', 'any');
		return $this;
	}

	// -----------------

	public function integrity(string $value): Link
	{
		$this->setAttribute('integrity', trim($value));
		return $this;
	}

	/**
	 * Can be either `anonymous` or `use-credentials`.
	 */
	public function crossOrigin(string $value): Link
	{
		$this->setAttribute('crossorigin', trim($value));
		return $this;
	}

	// -----------------

	protected function formatAsHtml(): string
	{
		$attributes = [];

		foreach ($this->config->array('attributes') as $name => $value) {
			if ($name === 'href' && $this->config->has('version')) {
				$version = $this->config->get('version', moment()->toEpochString());
				$value = $this->valueWithVersion($value, $version);
			}
			if ($value === null) {
				$attributes[$name] = $name;
				continue;
			}

			$attributes[$name] = sprintf('%s="%s"', $name, $value);
		}

		return sprintf('<link %s />', implode(' ', $attributes)) . PHP_EOL;
	}

	// -----------------

	protected function valueWithVersion(string $value, string $key): string
	{
		if (Str::contains($value, [':version'])) {
			return Str::replace($value, ':version', $key);
		}

		if (Str::contains($value, ['version='])) {
			return $value;
		}

		if (Str::contains($value, '?')) {
			return Str::finish($value, '&version=' . $key);
		}

		return Str::finish($value, '?version=' . $key);
	}

}