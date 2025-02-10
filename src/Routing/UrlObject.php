<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use JsonSerializable;
use Rovota\Framework\Routing\Traits\UrlModifiers;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Url;
use Stringable;

final class UrlObject implements Stringable, JsonSerializable
{
	use UrlModifiers;

	// -----------------

	public readonly UrlObjectConfig $config;

	// -----------------

	public function __construct(mixed $data = [])
	{
		$this->config = new UrlObjectConfig();

		foreach (convert_to_array($data) as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function __toString(): string
	{
		return $this->build();
	}

	public function __get(string $name)
	{
		return $this->config->{$name};
	}

	public function __set(string $name, $value): void
	{
		$this->config->set($name, $value);
	}

	// -----------------

	public function jsonSerialize(): string
	{
		return $this->__toString();
	}

	// -----------------

	public static function from(string $url): UrlObject
	{
		$object = new UrlObject();

		// Extract scheme
		if (Str::contains($url, '://')) {
			$object->withScheme(Str::before($url, '://'));
			$url = Str::after($url, '://');
		}

		// Extract fragment
		if (Str::contains($url, '#')) {
			$object->withFragment(Str::afterLast($url, '#'));
			$url = Str::beforeLast($url, '#');
		}

		// Extract query parameters
		if (Str::contains($url, '?')) {
			$parameters = Str::after($url, '?');
			$object->withParameters(Url::queryToArray($parameters));
			$url = Str::beforeLast($url, '?');
		}

		// Extract path
		if (Str::contains($url, '/')) {
			$object->withPath(Str::after($url, '/'));
			$url = Str::before($url, '/');
		}

		// Extract port number
		if (Str::contains($url, ':')) {
			$object->withPort((int) Str::after($url, ':'));
			$url = Str::before($url, ':');
		}

		$object->withDomain(strlen($url) > 0 ? $url : '-');

		return $object;
	}

	// -----------------

	public function copy(): UrlObject
	{
		return deep_clone($this);
	}

	// -----------------

	public function build(bool $relative = false): string
	{
		$path = $this->config->path;
		$parameters = $this->getParameterString();
		$fragment = $this->getFragmentString();

		if ($relative === true) {
			return $path.$parameters.$fragment;
		}

		if (Str::endsWith($path, '/')) {
			$path = Str::trimEnd($path, '/');
		}

		return $this->getHostString().$path.$parameters.$fragment;
	}

	// -----------------

	protected function getParameterString(): string|null
	{
		if (count($this->config->parameters) === 0) {
			return null;
		}

		$query = Url::arrayToQuery($this->config->parameters);
		return strlen($query) > 0 ? '?'.$query : '';
	}

	protected function getFragmentString(): string|null
	{
		if ($this->config->fragment === null) {
			return null;
		}
		return '#'.$this->config->fragment;
	}

	protected function getHostString(): string
	{
		$scheme = $this->config->scheme->value;

		if ($this->config->has('subdomain')) {
			$domain = $this->config->subdomain.'.'.$this->config->domain;
		} else {
			$domain = $this->config->domain;
		}

		$result = sprintf('%s://%s', $scheme, $domain);

		if ($this->config->port !== 80 && $this->config->port !== 443) {
			$result .= ':'.$this->config->port;
		}

		return $result;
	}

}