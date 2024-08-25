<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use JsonSerializable;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Interfaces\Arrayable;

class JsonResponse extends DefaultResponse
{
	public function __construct(JsonSerializable|array $content, StatusCode|int $status, Config $config)
	{
		parent::__construct($content, $status, $config);
	}

	// -----------------

	protected function getPrintableContent(): string|null
	{
		return json_encode_clean($this->content);
	}

	protected function prepareForPrinting(): void
	{
		$this->setContentType('application/json; charset=UTF-8');

		$this->content = match (true) {
			$this->content instanceof JsonSerializable => $this->content->jsonSerialize(),
			$this->content instanceof Arrayable => $this->content->toArray(),
			default => $this->content
		};
	}

}