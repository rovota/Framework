<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use BackedEnum;
use Rovota\Framework\Http\RequestManager;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Structures\Config;
use Rovota\Framework\Support\Str;

/**
 * @property Scheme $scheme
 * @property string|null $subdomain
 * @property string $domain
 * @property int $port
 * @property string $path
 * @property array $parameters
 * @property string|null $fragment
 */
class UrlObjectConfig extends Config
{

	protected function getScheme(): BackedEnum
	{
		return $this->enum('scheme', Scheme::class, Scheme::Https);
	}

	protected function setScheme(Scheme|string $scheme): void
	{
		if (is_string($scheme)) {
			$scheme = Scheme::tryFrom($scheme) ?? Scheme::Https;
		}

		$this->set('scheme', $scheme);
	}

	// -----------------

	protected function getSubdomain(): string|null
	{
		return $this->get('subdomain');
	}

	protected function setSubdomain(string|null $subdomain): void
	{
		if ($subdomain === null) {
			$this->remove('subdomain');
			return;
		}

		if ($this->get('domain') === null) {
			$this->setDomain(RequestManager::getCurrent()->targetHost());
		}

		$subdomain = trim($subdomain);

		// Set to null when unusable value is given.
		if (mb_strlen($subdomain) === 0) {
			$this->remove('subdomain');
			return;
		}

		// Set to null when useless value is given.
		if ($subdomain === 'www' || $subdomain === '.' || $subdomain === '-') {
			$this->remove('subdomain');
			return;
		}

		$this->set('subdomain', $subdomain);
	}

	protected function getDomain(): string
	{
		return $this->string('domain', RequestManager::getCurrent()->targetHost());
	}

	protected function setDomain(string $domain): void
	{
		$domain = trim($domain);

		if (mb_strlen($domain) === 0 || $domain === '-') {
			$this->setDomain(RequestManager::getCurrent()->targetHost());
			return;
		}

		if (Str::occurrences($domain, '.') > 1) {
			$this->setSubdomain(Str::before($domain, '.'));
			$domain = Str::after($domain, '.');
		}

		$this->set('domain', $domain);
	}

	protected function getPort(): int
	{
		return $this->int('port', RequestManager::getCurrent()->port());
	}

	// -----------------

	protected function getPath(): string
	{
		return Str::start($this->string('path'), '/');
	}

	protected function setPath(string $path): void
	{
		$path = trim($path, ' /');

		// Set to null when unusable value is given.
		if (mb_strlen($path) === 0 || $path === '-') {
			$this->remove('path');
			return;
		}

		$this->set('path', $path);
	}

	protected function getParameters(): array
	{
		return $this->array('parameters');
	}

	protected function setParameters(array $parameters): void
	{
		if (empty($parameters)) {
			$this->remove('parameters');
			return;
		}

		foreach ($parameters as $name => $value) {
			$name = 'parameters.'.strtolower(trim($name));
			if ($value === null) {
				$this->remove($name);
				continue;
			}
			$this->set($name, $value);
		}
	}

	protected function getFragment(): string|null
	{
		return $this->get('fragment');
	}

	protected function setFragment(string|null $fragment): void
	{
		if ($fragment === null) {
			$this->remove('fragment');
			return;
		}

		$fragment = trim($fragment);

		// Set to null when unusable value is given.
		if (mb_strlen($fragment) === 0 || $fragment === '-') {
			$this->remove('fragment');
			return;
		}

		$this->set('fragment', $fragment);
	}

}