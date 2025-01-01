<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Facades\Language;
use Rovota\Framework\Support\Path;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\Interfaces\ViewInterface;

class MailView extends DefaultView
{

	public static function make(array $variables = []): ViewInterface|static
	{
		$view = ViewManager::instance()->createMailView(null, static::class);

		foreach ($variables as $name => $value) {
			$view->with($name, $value);
		}

		return $view;
	}

	// -----------------

	protected function getTemplatePath(): string
	{
		$locale = $this->getVariables()->string('mail_template_locale', Language::current()->locale);

		$file = Str::replace($this->template, '.', '/');
		$file = Str::start($file, 'resources/templates/mail/' . $locale . '/');
		$file = Str::finish($file, '.php');

		$path = Path::toProjectFile($file);

		if (!file_exists($path)) {
			$path = Str::replace($path, $locale . '/', '');
		}

		if (!file_exists($path)) {
			$path = Str::replace($path, 'mail/', '');
		}

		return $path;
	}

}