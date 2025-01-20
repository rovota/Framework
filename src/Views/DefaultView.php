<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Support\Buffer;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\Interfaces\ViewInterface;
use Rovota\Framework\Views\Traits\ViewFunctions;
use Stringable;

class DefaultView implements Stringable, ViewInterface
{
	use ViewFunctions;

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

		$this->configuration();
	}

	public function __toString(): string
	{
		Buffer::end();

		$this->prepareForPrinting();

		return $this->getPrintableContent() ?? '';
	}

	// -----------------

	public static function make(array $variables = []): ViewInterface|static
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
		$file = Str::start($file, 'resources/templates/');
		$file = Str::finish($file, '.php');

		return Path::toProjectFile($file);
	}

	// -----------------

	protected function getPrintableContent(): string|null
	{
		Buffer::start();

		extract($this->config->array('variables'));

		include $this->getTemplatePath();

		return Buffer::retrieveAndErase();
	}

	protected function prepareForPrinting(): void
	{

	}

	protected function configuration(): void
	{

	}

}