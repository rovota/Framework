<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Interfaces;

interface MailSupportsCode
{

	public function code(string $code): static;

}