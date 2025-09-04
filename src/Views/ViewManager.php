<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views;

use Rovota\Framework\Facades\Language;
use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Views\Components\Link;
use Rovota\Framework\Views\Components\Meta;
use Rovota\Framework\Views\Components\Script;

/**
 * @internal
 */
final class ViewManager extends ServiceProvider
{
	protected Bucket $links;
	protected Bucket $meta;
	protected Bucket $scripts;
	protected Bucket $variables;

	public View|null $current = null;

	// -----------------

	public function __construct()
	{
		$this->links = new Bucket();
		$this->meta = new Bucket();
		$this->scripts = new Bucket();
		$this->variables = new Bucket();

		if (Registry::bool('enable_generator_branding', true)) {
			$this->attachMeta('generator', [
				'name' => 'generator', 'content' => Registry::string('about.generator', 'Rovota Framework')
			]);
		}

		$this->attachMeta('og:locale', [
			'name' => 'og:locale', 'content' => Language::active()->locale ?? 'en_US',
		]);
		$this->attachMeta('og:type', [
			'name' => 'og:type', 'content' => 'website',
		]);
		$this->attachMeta('og:url', [
			'name' => 'og:url', 'content' => RequestManager::instance()->current()->urlWithoutParameters(),
		]);

		$this->applyDefaultMeta();
	}

	// -----------------

	public function createView(string|null $template, string|null $class = null): View
	{
		$this->current = $class !== null ? new $class() : new View($template);

		return $this->current;
	}

	// -----------------

	public function retrieveData(): array
	{
		return [
			'links' => $this->links->toArray(),
			'meta' => $this->meta->toArray(),
			'scripts' => $this->scripts->toArray(),
			'variables' => $this->variables->toArray(),
		];
	}

	// -----------------

	public function current(): View|null
	{
		return $this->current;
	}

	// -----------------

	public function attachLink(string $identifier, Link|array $attributes): Link
	{
		$link = $attributes instanceof Link ? $attributes : new Link($attributes);

		$this->links->set($identifier, $link);

		return $link;
	}

	public function attachMeta(string $identifier, Meta|array $attributes): Meta
	{
		$meta = $attributes instanceof Meta ? $attributes : new Meta($attributes);

		$this->meta->set($identifier, $meta);

		return $meta;
	}

	public function attachScript(string $identifier, Script|array $attributes): Script
	{
		$script = $attributes instanceof Script ? $attributes : new Script($attributes);

		$this->scripts->set($identifier, $script);

		return $script;
	}

	public function attachVariable(string $identifier, mixed $value): void
	{
		$this->variables->set($identifier, $value);
	}

	// -----------------

	protected function applyDefaultMeta(): void
	{
		$this->attachMeta('og:site_name', [
			'name' => 'og:site_name', 'content' => Registry::string('about.name'),
		]);
		$this->attachMeta('application-name', [
			'name' => 'application-name', 'content' => Registry::string('about.name'),
		]);

		$this->attachMeta('description', [
			'name' => 'description', 'content' => Registry::string('about.description'),
		]);
		$this->attachMeta('keywords', [
			'name' => 'keywords', 'content' => implode(',', Registry::array('about.keywords')),
		]);
		$this->attachMeta('author', [
			'name' => 'author', 'content' => Registry::string('about.author'),
		]);
	}

}