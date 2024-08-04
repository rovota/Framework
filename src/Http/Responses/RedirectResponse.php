<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Responses;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\RequestManager;
use Rovota\Framework\Http\Response;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Structures\Config;
use Rovota\Framework\Support\Url;

class RedirectResponse extends Response
{

	protected UrlObject $location;

	// -----------------

	public function __construct(UrlObject|string|null $content, StatusCode|int $status, Config $config)
	{
		$this->location = match(true) {
			$content instanceof UrlObject => $content,
			is_string($content) => UrlObject::fromString($content),
			default => RequestManager::current()->url()->stripParameters()
		};

		parent::__construct(null, $status, $config);
	}

	// -----------------

	protected function getPrintableContent(): string
	{
		return '';
	}

	protected function prepareForPrinting(): void
	{
		$this->header('Location', $this->location);
	}

	// -----------------

	public function to(string $path, array $parameters = []): static
	{
		$this->location = Url::local($path, $parameters);
		return $this;
	}

	public function away(string $location, array $parameters = []): static
	{
		$this->location = Url::foreign($location, $parameters);
		return $this;
	}

	public function reload(): static
	{
		$this->location = RequestManager::current()->url();
		return $this;
	}

	// -----------------

	// TODO: toRoute()

	// TODO: toPrevious()

	// TODO: toNext()

	// TODO: toIntended()

	// -----------------

	public function scheme(Scheme|string $scheme): static
	{
		$this->location->scheme($scheme);
		return $this;
	}

	// -----------------

	public function subdomain(string $subdomain): static
	{
		$this->location->subdomain($subdomain);
		return $this;
	}

	public function domain(string $domain): static
	{
		$this->location->domain($domain);
		return $this;
	}

	public function port(int $port): static
	{
		$this->location->port($port);
		return $this;
	}

	// -----------------

	public function path(string $path): static
	{
		$this->location->path($path);
		return $this;
	}

	public function parameters(array $parameters): static
	{
		$this->location->parameters($parameters);
		return $this;
	}

	public function parameter(string $name, mixed $value): static
	{
		$this->location->parameter($name, $value);
		return $this;
	}

	public function fragment(string $fragment): static
	{
		$this->location->fragment($fragment);
		return $this;
	}

}