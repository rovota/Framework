<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Localization;

use Rovota\Framework\Structures\Bucket;
use Rovota\Framework\Support\Internal;

final class Language
{

	protected string $locale;

	protected Bucket $data;

	// -----------------

	protected array $translations = [];

	// -----------------

	public function __construct(string $locale, array $data = [])
	{
		$this->locale = $locale;
		$this->data = new Bucket($data);
	}

	// -----------------

	public function loadTranslations(): void
	{
		$file = Internal::projectFile('/resources/translations/'.$this->locale.'.json');

		if (file_exists($file)) {
			$contents = file_get_contents($file);
			$this->translations = array_filter(json_decode($contents, true));
		}
	}

	// -----------------

	public function textDirection(): string
	{
		return $this->data->string('about.direction');
	}

	// -----------------

	public function locale(): string
	{
		return $this->locale;
	}

	public function data(): Bucket
	{
		return $this->data;
	}

	public function about(): Bucket
	{
		return Bucket::from($this->data->get('about'));
	}

	public function units(): Bucket
	{
		return Bucket::from($this->data->get('units'));
	}

	// -----------------

	public function findTranslation(string $key): string|null
	{
		return $this->translations[$key] ?? null;
	}

}