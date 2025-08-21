<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Components;

final class Meta extends Asset
{

	public function charset(string $value = 'utf-8'): Meta
	{
		$this->setAttribute('charset', trim($value));
		return $this;
	}

	public function name(string $value): Meta
	{
		$this->setAttribute('name', trim($value));
		return $this;
	}

	public function content(string $value): Meta
	{
		$this->setAttribute('content', trim($value));
		return $this;
	}

	public function media(string $value): Meta
	{
		$this->setAttribute('media', trim($value));
		return $this;
	}

	// -----------------

	protected function formatAsHtml(): string
	{
		$attributes = [];

		foreach ($this->config->array('attributes') as $name => $value) {
			if ($value === null) {
				$attributes[$name] = $name;
				continue;
			}

			$attributes[$name] = sprintf('%s="%s"', $name, $value);
		}

		return sprintf('<meta %s />', implode(' ', $attributes)) . PHP_EOL;
	}

}