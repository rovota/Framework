<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Kernel\Application;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;

final class Request
{
	protected static RequestObject $current;

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
		self::$current = new RequestObject([
			'headers' => getallheaders(),
			'body' => self::getRequestBody(),
			'post' => self::getRequestPostData(),
			'query' => self::getRequestQueryData(),
		]);
	}

	// -----------------

	public static function current(): RequestObject
	{
		return self::$current;
	}

	// -----------------

	protected static function getRequestBody(): string|null
	{
		$body = file_get_contents('php://input');
		return $body === false ? null : trim($body);
	}

	protected static function getRequestPostData(): array
	{
		$data = $_POST;
		array_walk_recursive($data, function(&$item) {
			if (is_string($item)) {
				$item = mb_strlen(trim($item)) > 0 ? trim($item) : null;
			}
		});

		// TODO: Implement request files processing.

//		$files = FilesArrayOrganizer::organize($_FILES);

//		return array_merge($data, $files);
		return $data;
	}

	protected static function getRequestQueryData(): array
	{
		$url = Application::environment()->server->get('REQUEST_URI');

		if (Str::contains($url, '?')) {
			parse_str(Str::after($url, '?'), $parameters);

			return Arr::map($parameters, function ($value) {
				return $value === null ? null : trim($value);
			});
		}

		return [];
	}

}