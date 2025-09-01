<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Config;

class FileResponse extends DefaultResponse
{

	public function __construct(File $content, StatusCode|int $status, Config $config)
	{
		parent::__construct($content, $status, $config);
	}

	// -----------------

	protected function render(): string|null
	{
		return (string)$this->content;
	}

	protected function prepareRendering(): void
	{
		if ($this->content instanceof File) {
			$this->setContentType($this->content->properties->mime_type);
		}
	}

}