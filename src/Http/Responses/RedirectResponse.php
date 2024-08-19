<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Responses;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\RequestManager;
use Rovota\Framework\Http\Response;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Structures\Config;
use Rovota\Framework\Support\Url;

class RedirectResponse extends Response
{

	public UrlObject $location;

	// -----------------

	public function __construct(UrlObject|string|null $content, StatusCode|int $status, Config $config)
	{
		$this->location = match(true) {
			$content instanceof UrlObject => $content,
			is_string($content) => UrlObject::from($content),
			default => RequestManager::getCurrent()->url()->stripParameters()
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
		$this->location = RequestManager::getCurrent()->url();
		return $this;
	}

	// -----------------

	// TODO: toRoute()

	// TODO: toPrevious()

	// TODO: toNext()

	// TODO: toIntended()

}