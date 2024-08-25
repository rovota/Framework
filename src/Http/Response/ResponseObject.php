<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response;

use BackedEnum;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\Traits\ResponseModifiers;
use Rovota\Framework\Support\Config;
use Stringable;

class ResponseObject implements Stringable
{
	use ResponseModifiers;

	// -----------------

	public mixed $content;

	public StatusCode $status;

	public readonly Config $config;

	// -----------------

	public function __construct(mixed $content, StatusCode|int $status, Config $config)
	{
		$this->content = $content;
		$this->config = $config;

		$this->withStatus($status);
	}

	public function __toString(): string
	{
		ob_end_clean();

		$this->prepareForPrinting();

		$this->applyStatusCode();
		$this->applyHeaders();
		$this->applyCookies();

		return $this->getPrintableContent() ?? '';
	}

	// -----------------

	protected function applyCookies(): void
	{
		foreach ($this->config->array('cookies') as $cookie) {
			$cookie->apply();
		}
	}

	protected function applyStatusCode(): void
	{
		if ($this->content instanceof StatusCode) {
			http_response_code($this->content->value);
			return;
		}

		if (is_int($this->content) && StatusCode::tryFrom($this->content) instanceof BackedEnum) {
			http_response_code($this->content);
			return;
		}

		http_response_code($this->status->value);
	}

	protected function applyHeaders(): void
	{
		foreach ($this->config->array('headers') as $name => $value) {
			if (strlen($value) > 0) {
				header(sprintf('%s: %s', $name, $value));
			}
		}
	}

	protected function getPrintableContent(): string|null
	{
		if ($this->content instanceof Stringable) {
			return $this->content->__toString();
		}

		return (string) $this->content;
	}

	protected function prepareForPrinting(): void
	{

	}

}