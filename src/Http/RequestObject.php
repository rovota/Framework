<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Http\Traits\RequestInput;
use Rovota\Framework\Kernel\Application;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Str;

final class RequestObject
{
	use RequestInput;

	protected RequestHeaders $headers;

	protected UrlObject $url;

	// -----------------

	public function __construct(mixed $data = [])
	{
		$this->headers = new RequestHeaders(array_change_key_case($data['headers']));
		$this->url = UrlObject::fromString($this->getFullUrlString());

		$this->body = $data['body'];
		$this->post = new RequestData($data['post']);
		$this->query = new RequestData($data['query']);

		foreach (convert_to_array($data) as $key => $value) {
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	// -----------------

	public function headers(): RequestHeaders
	{
		return $this->headers;
	}

	/**
	 * Relies on the experimental `Sec-CH-UA-Platform` HTTP header.
	 * For non-supported clients, it will return `Unknown`.
	 */
	public function platform(): string
	{
		return trim($this->headers->string('Sec-CH-UA-Platform', 'Unknown'), '"');
	}

	/**
	 * Only available when using Cloudflare. Paid plan may be required.
	 */
	public function hasExposedCredentials(): bool
	{
		return $this->headers->bool('Exposed-Credential-Check');
	}


	/**
	 * Relies on the experimental `Sec-GPC` HTTP header.
	 */
	public function hasPrivacyControl(): bool
	{
		return $this->headers->bool('Sec-GPC');
	}

	/**
	 * The Do Not Track specification has been discontinued. Clients amy remove it in the future.
	 */
	public function hasDoNotTrack(): bool
	{
		return $this->headers->bool('DNT');
	}

	// -----------------

	public function url(): UrlObject
	{
		return $this->url;
	}

	public function urlWithoutParameters(): UrlObject
	{
		return $this->url->copy()->setParameters([]);
	}

	public function scheme(): Scheme
	{
		return $this->url->getScheme();
	}

	public function domain(): string
	{
		return $this->url->getDomain();
	}

	public function port(): int
	{
		return $this->url->getPort();
	}

	public function path(): string
	{
		return $this->url->getPath() ?? '/';
	}

	public function pathMatchesPattern(string $pattern): bool
	{
		$pattern = preg_replace('/\*/', '(.+)', $pattern);
		$pattern = preg_replace('/{(.*?)}/', '(.+)', $pattern);
		return preg_match_all('#^' . $pattern . '$#', $this->path()) === 1;
	}

	// -----------------

	public function targetHost(): string
	{
		return Application::$server->get('HTTP_HOST');
	}

	public function remoteHost(): string
	{
		return Application::$server->get('REMOTE_HOST');
	}

	// -----------------

	public function realMethod(): RequestMethod
	{
		$method = Application::$server->get('REQUEST_METHOD');
		return RequestMethod::tryFrom($method) ?? RequestMethod::Get;
	}

	public function method(): RequestMethod
	{
		$method = $this->realMethod();

		if ($method === RequestMethod::Post && $this->headers->has('X-HTTP-Method-Override')) {
			$value = $this->headers->enum('X-HTTP-Method-Override', RequestMethod::class);
			if (RequestMethod::tryFrom($value) !== null) {
				$method = $value;
			}
		}

		return $method;
	}

	public function isMethod(RequestMethod|string $method): bool
	{
		if (is_string($method)) {
			$method = RequestMethod::tryFrom(strtoupper($method));
		}

		return $this->method() === $method;
	}

	public function isPost(): bool
	{
		return $this->method() === RequestMethod::Post;
	}

	// -----------------

	public function isSecure(): bool
	{
		return $this->scheme() === Scheme::Https || Application::$server->get('HTTPS') === 'on';
	}

	public function isProxy(): bool
	{
		$headers = ['Forwarded', 'X-Forwarded-For', 'Client-IP'];

		foreach ($headers as $header) {
			if ($header === 'X-Forwarded-For' && $this->headers->has('CF-Connecting-IP')) {
				if (str_contains($this->headers->string($header), ',')) {
					return true;
				}
				continue;
			}
			if ($this->headers->has($header)) {
				return true;
			}
		}

		return false;
	}

	public function isXmlHttp(): bool
	{
		return $this->headers->get('X-Requested-With') === 'XMLHttpRequest';
	}

	public function isJson(): bool
	{
		$value = $this->headers->get('Content-Type', '');
		return text($value)->lower()->contains('json');
	}

	// -----------------

	public function protocol(): string
	{
		return Application::$server->get('SERVER_PROTOCOL');
	}

	public function format(): string|null
	{
		return $this->headers->get('Content-Type');
	}

	// -----------------

	public function referrer(): string|null
	{
		return $this->headers->get('Referer');
	}

	public function useragent(): string|null
	{
		return $this->headers->get('User-Agent');
	}

	public function ip(): string
	{
		return match(true) {
			$this->headers->has('CF-Connecting-IP') => $this->headers->get('CF-Connecting-IP'),
			$this->headers->has('X-Forwarded-For') => $this->headers->get('X-Forwarded-For'),
			default => Application::$server->get('REMOTE_ADDR'),
		};
	}

	/**
	 * Requires either the Cloudflare IPCountry feature or the GeoIP PHP extension.
	 */
	public function country(): string|null
	{
		if ($this->headers->has('CF-IPCountry')) {
			$code = $this->headers->get('CF-IPCountry');
			return ($code === 'XX' || $code === 'T1') ? null : $code;
		}

		if (function_exists('geoip_country_code_by_name')) {
			$country_code = geoip_country_code_by_name($this->ip());
			return $country_code === false ? null : $country_code;
		}

		return null;
	}

	/**
	 * Uses the experimental `Sec-CH-UA-Model` HTTP header.
	 * For non-supported clients, it will attempt to guess/approximate the device name using the useragent.
	 * If nothing useful can be found, `Unknown` will be returned.
	 */
	public function device(): string
	{
		if ($this->headers->has('Sec-CH-UA-Model')) {
			return $this->headers->get('Sec-CH-UA-Model');
		}

		return $this->getApproximateDeviceFromUserAgent();
	}

	// -----------------

	public function hasCredentials(): bool
	{
		return $this->username() !== null && $this->password() !== null;
	}

	public function username(): string|null
	{
		$username = Application::$server->get('PHP_AUTH_USER');
		return Str::length($username) > 0 ? $username : null;
	}

	public function password(): string|null
	{
		$password = Application::$server->get('PHP_AUTH_PW');
		return Str::length($password) > 0 ? $password : null;
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	protected function getFullUrlString(): string
	{
		$scheme = Application::$server->get('REQUEST_SCHEME', 'https');
		$host = Application::$server->get('HTTP_HOST', 'localhost');
		$port = Application::$server->get('SERVER_PORT', '80');
		$path = Str::before(Application::$server->get('REQUEST_URI'), '?');
		$query = Application::$server->get('QUERY_STRING');

		return sprintf('%s://%s:%s%s', $scheme, $host, $port, $path. (strlen($query) > 0 ? '?' : '') .$query);
	}

	protected function getApproximateDeviceFromUserAgent(): string
	{
		$useragent = $this->headers->text('User-Agent')->remove([
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

}