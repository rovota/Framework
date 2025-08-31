<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Handlers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class StackHandler extends AbstractProcessingHandler
{

	public function __construct()
	{
		parent::__construct();
	}

	// -----------------

	protected function write(LogRecord $record): void
	{
	}

}