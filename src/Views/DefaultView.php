<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\Interfaces\ViewInterface;
use Rovota\Framework\Views\Traits\ViewFunctions;
use Rovota\Framework\Views\Traits\WebFunctions;
use Stringable;

class DefaultView implements Stringable, ViewInterface
{
	use ViewFunctions, WebFunctions;

	// -----------------

	protected ViewConfig $config;

	protected string|null $template = null;

	// -----------------

	public function __construct(string|null $template, ViewConfig $config)
	{
		$this->config = $config;

		if ($template !== null) {
			$this->template = $template;
		}
	}

	public function __toString(): string
	{
		ob_end_clean();

		$this->prepareForPrinting();

		return $this->getPrintableContent() ?? '';
	}

	// -----------------

	public function getConfig(): ViewConfig
	{
		return $this->config;
	}

	// -----------------

	public static function make(array $variables = []): static
	{
		$view = ViewManager::instance()->createView(null, static::class);

		foreach ($variables as $name => $value) {
			$view->with($name, $value);
		}

		return $view;
	}

	// -----------------

	protected function getTemplatePath(): string
	{
		$file = Str::replace($this->template, '.', '/');
		$file = Str::finish($file, '.php');
		$file = Str::start($file, 'resources/templates/');

		return Path::toProjectFile($file);
	}

	// -----------------

	protected function getPrintableContent(): string|null
	{
		ob_start();

		extract($this->config->array('variables'));

		include $this->getTemplatePath();

		return ob_get_clean();
	}

	protected function prepareForPrinting(): void
	{

	}

}