<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Errors\Error;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;
use Throwable;

class ErrorResponse extends DefaultResponse
{

	public function __construct(Throwable|Error|array $content, StatusCode|int $status, Config $config)
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

		$result = match (true) {
			is_array($this->content) => $this->getContentAsArray($this->content),
			$this->content instanceof Throwable => [$this->getThrowableAsArray($this->content)],
			$this->content instanceof Error => [$this->getErrorAsArray($this->content)],
			default => $this->content
		};

		$this->content = [
			'timestamp' => now(),
			'errors' => $result,
		];
	}

	// -----------------

	protected function getContentAsArray(array $content): array
	{
		$result = [];

		foreach ($content as $item) {
			if ($item instanceof Throwable) {
				$result[] = $this->getThrowableAsArray($item);
			}
			if ($item instanceof Error) {
				$result[] = $this->getErrorAsArray($item);
			}
		}

		return $result;
	}

	protected function getThrowableAsArray(Throwable $throwable): array
	{
		return [
			'type' => Str::afterLast($throwable::class, '\\'),
			'code' => $throwable->getCode(),
			'message' => Str::translate($throwable->getMessage()),
		];
	}

	protected function getErrorAsArray(Error $error): array
	{
		return [
			'type' => Str::afterLast($error::class, '\\'),
			'code' => $error->code,
			'message' => Str::translate($error->message, $error->parameters),
		];
	}

}