<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Support\Config;

class MailConfig extends Config
{

	public string $default {
		get => $this->string('default', array_key_first($this->array('mailers')) ?? '---');
	}

	public array $mailers {
		get => $this->array('mailers');
	}

}