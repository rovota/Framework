<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Url;

class RedirectResponse extends DefaultResponse
{

	public UrlObject $location;

	// -----------------

	public function __construct(UrlObject|string|null $content, StatusCode|int $status, Config $config)
	{
		$this->location = match (true) {
			$content instanceof UrlObject => $content,
			is_string($content) => UrlObject::from($content),
			default => RequestManager::instance()->current()->url()->stripParameters()
		};

		parent::__construct(null, $status, $config);
	}

	// -----------------

	protected function getPrintableContent(): string|null
	{
		return null;
	}

	protected function prepareForPrinting(): void
	{
		$this->withHeader('Location', $this->location);
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
		$this->location = RequestManager::instance()->current()->url();
		return $this;
	}

	// -----------------

	public function withInput(array $keys = []): static
	{
		if (empty($keys)) {
			RequestManager::instance()->current()->keep();
			return $this;
		}

		RequestManager::instance()->current()->keepOnly($keys);
		return $this;
	}

	public function withInputExcept(array $keys): static
	{
		RequestManager::instance()->current()->keepExcept($keys);
		return $this;
	}

	// -----------------

	public function toRoute(string $name, array $context = [], array $parameters = []): static
	{
		$this->location = Url::route($name, $context, $parameters);
		return $this;
	}

	public function toPrevious(string $default = '/'): static
	{
		$this->location = Url::previous($default);
		return $this;
	}

	public function toNext(string $default = '/'): static
	{
		$this->location = Url::next($default);
		return $this;
	}

	public function toIntended(string $default = '/'): static
	{
		$this->location = Url::intended($default);
		return $this;
	}

}