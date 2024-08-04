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

	// -----------------

	protected function getSubdomain(): string|null
	{
		return $this->get('subdomain');
	}

	protected function getDomain(): string
	{
		return $this->string('domain', RequestManager::current()->targetHost());
	}

	protected function getPort(): int
	{
		return $this->int('port', RequestManager::current()->port());
	}

	// -----------------

	protected function getPath(): string
	{
		return Str::start($this->string('path'), '/');
	}

	protected function getParameters(): array
	{
		return $this->array('parameters');
	}

	protected function getFragment(): string|null
	{
		return $this->get('fragment');
	}

}