<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Facades\Language;
use Rovota\Framework\Support\Buffer;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\Traits\ViewFunctions;
use Stringable;

class View implements Stringable
{
	use ViewFunctions;

	// -----------------

	protected ViewConfig $config;

	protected string|null $template = null;

	// -----------------

	public function __construct(string|null $template = null)
	{
		$this->config = new ViewConfig();

		if ($template !== null) {
			$this->template = $template;
		}

		$this->configuration();
	}

	public function __toString(): string
	{
		Buffer::end();

		$this->prepareData();
		$this->prepareRendering();

		ViewManager::instance()->current = $this;

		return $this->render() ?? '';
	}

	// -----------------

	protected function prepareData(): void
	{
		$this->config->merge(ViewManager::instance()->retrieveData());
	}

	protected function prepareRendering(): void
	{

	}

	protected function configuration(): void
	{

	}

	// -----------------

	protected function render(): string|null
	{
		Buffer::start();

		extract($this->config->array('variables'));

		include $this->getTemplatePath();

		return Buffer::retrieveAndErase();
	}

	protected function getTemplatePath(): string
	{
		$locale = $this->config->string('variables.template_locale', Language::active()->locale);

		$file = Str::replace($this->template, '.', '/');

		if (str_contains($file, '{locale}')) {
			$file = str_replace('{locale}', $locale, $file);
		} else {
			$file = Str::start($file, $locale . '/');
		}

		$file = Str::start($file, 'resources/templates/');
		$file = Str::finish($file, '.php');

		$path = Path::toProjectFile($file);

		if (!file_exists($path)) {
			$path = Str::replace($path, $locale . '/', '');
		}

		return $path;
	}

}