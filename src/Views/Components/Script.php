<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Components;

use Rovota\Framework\Support\Str;

final class Script extends Asset
{

	public function version(string $value): Script
	{
		$this->config->set('version', trim($value));
		return $this;
	}

	// -----------------

	public function content(string $value): Script
	{
		$this->config->set('content', trim($value));
		return $this;
	}

	// -----------------

	public function source(string $url): Script
	{
		$this->setAttribute('src', trim($url));
		return $this;
	}

	public function type(string $value): Script
	{
		$this->setAttribute('type', trim($value));
		return $this;
	}

	// -----------------

	public function defer(): Script
	{
		$this->setAttribute('defer', null);
		return $this;
	}

	public function async(): Script
	{
		$this->setAttribute('async', null);
		return $this;
	}

	// -----------------

	public function integrity(string $value): Script
	{
		$this->setAttribute('integrity', trim($value));
		return $this;
	}

	/**
	 * Can be either `anonymous` or `use-credentials`.
	 */
	public function crossOrigin(string $value): Script
	{
		$this->setAttribute('crossorigin', trim($value));
		return $this;
	}

	// -----------------

	protected function formatAsHtml(): string
	{
		$attributes = [];

		foreach ($this->config->array('attributes') as $name => $value) {
			if ($name === 'src' && $this->config->has('version')) {
				$version = $this->config->get('version', moment()->toEpochString());
				$value = $this->valueWithVersion($value, $version);
			}
			if ($name === 'src' && $this->config->has('version')) {
				$value = str_replace(':version', $this->config->get('version'), $value);
			}
			if ($value === null) {
				$attributes[$name] = $name;
				continue;
			}

			$attributes[$name] = sprintf('%s="%s"', $name, $value);
		}

		return sprintf('<script %s>%s</script>', implode(' ', $attributes), $this->config->get('content')) . PHP_EOL;
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