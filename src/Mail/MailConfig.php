<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail;

use Rovota\Framework\Support\Config;

/**
 * @property-read string $default
 * @property-read array $mailers
 */
class MailConfig extends Config
{

	protected function getDefault(): string
	{
		return $this->string('default', array_key_first($this->array('mailers')) ?? '---');
	}

	protected function getMailers(): array
	{
		return $this->array('mailers');
	}

}