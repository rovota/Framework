<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Drivers;

use Rovota\Framework\Mail\Handlers\SmtpHandler;
use Rovota\Framework\Mail\Mailer;
use Rovota\Framework\Mail\MailerConfig;

class Smtp extends Mailer
{

	public function __construct(string $name, MailerConfig $config)
	{
		$handler = new SmtpHandler($config->parameters);

		parent::__construct($name, $handler, $config);
	}

}