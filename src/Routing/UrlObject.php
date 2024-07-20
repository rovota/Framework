<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Routing\Traits\UrlAccessors;
use Rovota\Framework\Routing\Traits\UrlModifiers;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Url;
use Stringable;

final class UrlObject implements Stringable
{
	use UrlAccessors, UrlModifiers;

	// -----------------

	protected Scheme $scheme = Scheme::Https;

	protected string|null $subdomain = null;
	protected string|null $domain = null;
	protected int|null $port = null;

	protected string|null $path = null;
	protected array $parameters = [];
	protected string|null $fragment = null;

	// -----------------

	public function __construct(mixed $data = [])
	{
		$data = convert_to_array($data);

		foreach ($data as $key => $value) {
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function __toString(): string
	{
		return $this->build();
	}

	public function copy(): UrlObject
	{
		return clone $this;
	}

	// -----------------

	public static function fromString(string $url): UrlObject
	{
		$object = new UrlObject();

		// Extract scheme
		if (Str::contains($url, '://')) {
			$object->setScheme(Str::before($url, '://'));
			$url = Str::after($url, '://');
		}

		// Extract fragment
		if (Str::contains($url, '#')) {
			$object->setFragment(Str::afterLast($url, '#'));
			$url = Str::beforeLast($url, '#');
		}

		// Extract query parameters
		if (Str::contains($url, '?')) {
			$parameters = Str::after($url, '?');
			$object->setParameters(Url::queryToArray($parameters));
			$url = Str::beforeLast($url, '?');
		}

		// Extract path
		if (Str::contains($url, '/')) {
			$object->setPath(Str::after($url, '/'));
			$url = Str::before($url, '/');
		}

		// Extract port number
		if (Str::contains($url, ':')) {
			$object->setPort((int)Str::after($url, ':'));
			$url = Str::before($url, ':');
		}

		$object->setDomain($url);

		return $object;
	}

	// -----------------

	public function build(bool $relative = false): string
	{
		$parameters = $this->getParameterString();
		$fragment = $this->getFragmentString();
		$result = $this->path.$parameters.$fragment;

		if (Str::startsWith($result, '?') === false && $this->path !== null) {
			$result = Str::start($result, '/');
		}

		if ($relative === true) {
			return $result;
		}

		return $this->getHostString().$result;
	}

	// -----------------

	protected function getParameterString(): string
	{
		return empty($this->parameters) ? '' : '?'.Url::arrayToQuery($this->parameters);
	}

	protected function getFragmentString(): string
	{
		return $this->fragment === null ? '' : '#'.$this->fragment;
	}

	protected function getHostString(): string
	{
		$scheme = $this->scheme->value.'://';

		$subdomain = $this->subdomain !== null ? $this->subdomain.'.' : '';
		$domain = $this->domain !== null ? $this->domain : ''; // TODO: Grab current host from Request object.

		if ($this->port !== 80 && $this->port !== 443) {
			$port = $this->port !== null ? ':'.$this->port : $this->port;
		} else {
			$port = null;
		}

		return $scheme.$subdomain.$domain.$port;
	}

}