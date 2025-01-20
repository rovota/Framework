<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Facades\Language;
use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Facades\Request;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;
use Rovota\Framework\Views\Interfaces\ViewInterface;

/**
 * @internal
 */
final class ViewManager extends ServiceProvider
{

	protected ViewConfig $config;

	protected ViewInterface|null $current = null;

	// -----------------

	public function __construct()
	{
		$this->config = new ViewConfig();

		if (Registry::bool('enable_generator_branding', true)) {
			$this->attachMeta('*', 'generator', [
				'name' => 'generator', 'content' => Registry::string('about.generator', 'Rovota Framework')
			]);
		}

		$this->attachMeta('*', 'og:locale', [
			'name' => 'og:locale', 'content' => Language::active()->locale ?? 'en_US',
		]);
		$this->attachMeta('*', 'og:type', [
			'name' => 'og:type', 'content' => 'website',
		]);
		$this->attachMeta('*', 'og:url', [
			'name' => 'og:url', 'content' => Request::current()->urlWithoutParameters(),
		]);

		$this->applyDefaultMeta();
	}

	// -----------------

	public function createView(string|null $template, string|null $class = null): ViewInterface
	{
		$config = new ViewConfig([
			'links' => $this->getDataForType('links', $template),
			'meta' => $this->getDataForType('meta', $template),
			'scripts' => $this->getDataForType('scripts', $template),
			'variables' => $this->getDataForType('variables', $template),
		]);

		if ($class !== null) {
			$this->current = new $class(null, $config);
		} else {
			$this->current = new DefaultView($template, $config);
		}

		return $this->current;
	}

	public function createMailView(string|null $template, string|null $class = null): ViewInterface
	{
		$config = new ViewConfig([
			'variables' => $this->getDataForType('variables', $template),
		]);

		if ($class !== null) {
			return new $class(null, $config);
		} else {
			return new MailView($template, $config);
		}
	}

	// -----------------

	public function current(): ViewInterface|null
	{
		return $this->current;
	}

	// -----------------

	public function hasLink(string $template, string $identifier): bool
	{
		return $this->config->has(sprintf('links.%s.%s', $template, $identifier));
	}

	public function attachLink(array|string $templates, string $identifier, Link|array $attributes): Link
	{
		$link = $attributes instanceof Link ? $attributes : new Link($attributes);

		if (is_string($templates)) {
			$templates = [$templates];
		}
		
		foreach ($templates as $template) {
			$key = sprintf('links.%s.%s', $template, $identifier);
			$this->config->set($key, $link);
		}

		return $link;
	}

	// -----------------

	public function hasMeta(string $template, string $identifier): bool
	{
		return $this->config->has(sprintf('meta.%s.%s', $template, $identifier));
	}

	public function attachMeta(array|string $templates, string $identifier, Meta|array $attributes): Meta
	{
		$meta = $attributes instanceof Meta ? $attributes : new Meta($attributes);

		if (is_string($templates)) {
			$templates = [$templates];
		}

		foreach ($templates as $template) {
			$key = sprintf('meta.%s.%s', $template, $identifier);
			$this->config->set($key, $meta);
		}

		return $meta;
	}

	// -----------------

	public function hasScript(string $template, string $identifier): bool
	{
		return $this->config->has(sprintf('scripts.%s.%s', $template, $identifier));
	}

	public function attachScript(array|string $templates, string $identifier, Script|array $attributes): Script
	{
		$script = $attributes instanceof Script ? $attributes : new Script($attributes);

		if (is_string($templates)) {
			$templates = [$templates];
		}

		foreach ($templates as $template) {
			$key = sprintf('scripts.%s.%s', $template, $identifier);
			$this->config->set($key, $script);
		}

		return $script;
	}

	// -----------------

	public function hasVariable(string $template, string $identifier): bool
	{
		return $this->config->has(sprintf('variables.%s.%s', $template, $identifier));
	}

	public function attachVariable(array|string $templates, string $identifier, mixed $value): void
	{
		if (is_string($templates)) {
			$templates = [$templates];
		}

		foreach ($templates as $template) {
			$key = sprintf('variables.%s.%s', $template, $identifier);
			$this->config->set($key, $value);
		}
	}

	public function updateVariable(array|string $templates, string $identifier, mixed $value): void
	{
		if (is_string($templates)) {
			$templates = [$templates];
		}

		foreach ($templates as $template) {
			$key = sprintf('variables.%s.%s', $template, $identifier);
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$this->config->set($key . '.' . $k, $v);
				}
			} else {
				$this->config->set($key, $value);
			}
		}
	}

	// -----------------

	protected function getDataForType(string $type, string|null $name): array
	{
		$items = array_map(function ($value) {
			return $value;
		}, $this->config->array($type . '.*'));

		if ($name !== null) {
			$levels = explode('.', $name);

			foreach ($levels as $level) {
				if (Str::endsWith($name, $level) === false) {
					$level = Str::before($name, Str::after($name, $level. '.')) . '*';
				} else {
					$level = $name;
				}

				foreach ($this->config->array($type . '.' . $level) as $key => $value) {
					$items[$key] = $value;
				}
			}
		}

		return $items;
	}

	// -----------------

	protected function applyDefaultMeta(): void
	{
		$this->attachMeta('*', 'og:site_name', [
			'name' => 'og:site_name', 'content' => Registry::string('about.name'),
		]);
		$this->attachMeta('*', 'application-name', [
			'name' => 'application-name', 'content' => Registry::string('about.name'),
		]);

		$this->attachMeta('*', 'description', [
			'name' => 'description', 'content' => Registry::string('about.description'),
		]);
		$this->attachMeta('*', 'keywords', [
			'name' => 'keywords', 'content' => implode(',', Registry::array('about.keywords')),
		]);
		$this->attachMeta('*', 'author', [
			'name' => 'author', 'content' => Registry::string('about.author'),
		]);
	}

}