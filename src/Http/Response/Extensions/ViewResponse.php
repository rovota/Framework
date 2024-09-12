<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response\Extensions;

use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Response\DefaultResponse;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Views\DefaultView;

class ViewResponse extends DefaultResponse
{

	public function __construct(DefaultView $content, StatusCode|int $status, Config $config)
	{
		parent::__construct($content, $status, $config);
	}

	// -----------------

	protected function prepareForPrinting(): void
	{
		$this->setContentType('text/html');
	}

}