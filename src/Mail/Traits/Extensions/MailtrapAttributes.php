<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Mail\Traits\Extensions;

use Rovota\Framework\Mail\Mailable;

trait MailtrapAttributes
{

	public function category(string $category): static
	{
		$category = trim($category);

		/** @var Mailable $this */
		$this->with('mail_category', $category);
		$this->withHeader('X-MT-Category', $category);

		return $this;
	}

}