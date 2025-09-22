<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request;

use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\CacheStore;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Storage\FilesArrayOrganizer;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Text;

/**
 * @internal
 */
final class RequestManager extends ServiceProvider
{

	protected RequestObject $current;

	// -----------------

	public function __construct()
	{
		$this->current = new RequestObject([
			'headers' => getallheaders(),
			'body' => $this->getRequestBody(),
			'post' => $this->getRequestPostData(),
			'query' => $this->getRequestQueryData(),
		]);

		$continue = $this->current->query->string('continue');

		if (mb_strlen($continue) > 0) {
			$store = CacheManager::instance()->getWithDriver(Driver::Session);
			if ($store instanceof CacheStore) {
				$store->set('location.next', $continue);
			}
		}
	}

	// -----------------

	public function current(): RequestObject
	{
		return $this->current;
	}

	// -----------------

	/**
	 * Attempts to retrieve a usable device name from a given useragent string. If nothing can be found, `Unknown` will be returned.
	 */
	public function getApproximateDeviceFromUserAgent(string $useragent): string
	{
		$useragent = Text::from($useragent)->remove([
			'; x64', '; Win64', '; WOW64', '; K', ' like Mac OS X', 'X11; '
		])->after('(')->before(')')->before('; rv');

		if ($useragent->contains('CrOS')) {
			return $useragent
				->after('CrOS ')
				->beforeLast(' ')
				->replace(['x86_64', 'armv7l', 'aarch64'], ['x86 64-bit', 'ARM 32-bit', 'ARM 64-bit'])
				->wrap('(', ')')
				->prepend('Chromebook ');
		}

		if ($useragent->contains('iPhone')) {
			return $useragent
				->after('iPhone OS ')
				->replace('_', '.')
				->wrap('(iOS ', ')')
				->prepend('iPhone ');
		}

		if ($useragent->contains('iPad')) {
			return $useragent
				->after('CPU OS ')
				->replace('_', '.')
				->wrap('(iPadOS ', ')')
				->prepend('iPad ');
		}

		if ($useragent->contains('Macintosh')) {
			return $useragent
				->after('OS X ')
				->replace('_', '.')
				->wrap('(MacOS ', ')')
				->prepend('Mac ');
		}

		if ($useragent->contains('Android')) {
			return $useragent->afterLast('; ');
		}

		if ($useragent->contains('Windows')) {
			return $useragent
				->replace(['NT 10.0', 'NT 6.3', 'NT 6.2'], ['10/11', '8.1', '8.0'])
				->before(';');
		}

		return 'Unknown';
	}

	// -----------------

	protected function getRequestBody(): string|null
	{
		$body = file_get_contents('php://input');
		return $body === false ? null : mb_trim($body);
	}

	protected function getRequestPostData(): array
	{
		$data = $_POST;
		array_walk_recursive($data, function (&$item) {
			if (is_string($item)) {
				$item = mb_strlen(mb_trim($item)) > 0 ? mb_trim($item) : null;
			}
		});

		$files = FilesArrayOrganizer::organize($_FILES);

		return array_merge($data, $files);
	}

	protected function getRequestQueryData(): array
	{
		$url = Framework::environment()->server->get('REQUEST_URI');

		if (Str::contains($url, '?')) {
			parse_str(Str::after($url, '?'), $parameters);

			return Arr::map($parameters, function ($value) {
				return $value === null ? null : mb_trim($value);
			});
		}

		return [];
	}

}