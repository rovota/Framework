<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Mail\Traits;

use Rovota\Framework\Views\Interfaces\ViewInterface;
use Rovota\Framework\Views\ViewManager;

trait MailableContent
{

	protected mixed $content = null;

	protected array $variables = [];

	// -----------------

	public function locale(string $locale): static
	{
		$this->attributes['locale'] = trim($locale);

		$this->with('mail_template_locale', $locale);
		return $this;
	}

	// -----------------

	public function view(string $template, string|null $class = null): static
	{
		$this->attributes['view'] = $template;

		$this->content = ViewManager::instance()->createMailView($template, $class);
		return $this;
	}

	// -----------------

	public function with(string $name, mixed $value): static
	{
		$this->attributes['data'][$name] = $value;
		$this->variables[$name] = $value;
		return $this;
	}

	public function summary(string $content): static
	{
		$this->variables['mail_summary'] = $content;
		return $this;
	}

	// -----------------

	protected function render(): string|null
	{
		if ($this->content === null) {
			return null;
		}

		if ($this->content instanceof ViewInterface) {
			$this->content->with($this->variables);
		}

		if (is_string($this->content)) {
			foreach ($this->variables as $name => $value) {
				$this->content = str_replace(sprintf('{%% %s %%}', $name), $value, $this->content);
			}
		}

		return (string)$this->content;
	}

}