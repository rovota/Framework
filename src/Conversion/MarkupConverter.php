<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;

final class MarkupConverter
{

	protected static array $languages = [];

	protected static array $conversions = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::addLanguage('md_cm', 'Markdown (CommonMark)');
		self::addLanguage('md_gfm', 'Markdown (GitHub)');

		self::addConversion('default', function ($string) {
			return self::getCommonMarkConverter()->convert($string);
		}, 'md_cm');

		self::addConversion('default', function ($string) {
			return self::getGitHubMarkdownConverter()->convert($string);
		}, 'md_gfm');
	}

	// -----------------

	public static function addLanguage(string $name, string $label): void
	{
		self::$languages[$name] = trim($label);
	}

	public static function removeLanguage(string $name): void
	{
		unset(self::$languages[$name]);
		unset(self::$conversions[$name]);
	}

	public static function hasLanguage(string $name): bool
	{
		return key_exists($name, self::$languages);
	}

	public static function getLanguages(): array
	{
		return self::$languages;
	}

	// -----------------

	public static function addConversion(string $name, callable $function, string|null $language = null): void
	{
		self::$conversions[$language ?? array_key_first(self::$languages)][$name] = $function;
	}

	public static function removeConversion(string $name, string|null $language = null): void
	{
		unset(self::$conversions[$language ?? array_key_first(self::$languages)][$name]);
	}

	public static function hasConversion(string $name, string|null $language = null): bool
	{
		return key_exists($name, self::$conversions[$language ?? array_key_first(self::$languages)]);
	}

	public static function clearConversions(string|null $language = null): void
	{
		self::$conversions[$language ?? array_key_first(self::$languages)] = [];
	}

	// -----------------

	public static function toHtml(string $string, string|null $language = null): string
	{
		$string = mb_trim($string);
		$language = $language ?? array_key_first(self::$languages);


		if (!key_exists($language, self::$languages)) {
			return $string;
		}

		foreach (self::$conversions[$language] as $callable) {
			$string = $callable($string);
		}

		return $string;
	}

	// -----------------

	protected static function getCommonMarkConverter(): CommonMarkConverter
	{
		return new CommonMarkConverter([
			'allow_unsafe_links' => false,
			'html_input' => 'strip',
		]);
	}

	protected static function getGitHubMarkdownConverter(): GithubFlavoredMarkdownConverter
	{
		return new GithubFlavoredMarkdownConverter([
			'allow_unsafe_links' => false,
			'html_input' => 'strip',
		]);
	}

}