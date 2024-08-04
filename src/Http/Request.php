<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Http\Traits\RequestInput;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Localization\Localization;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Http;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Url;

final class Request
{
	use RequestInput;

	protected RequestHeaders $headers;

	protected UrlObject $url;


	protected array|null $acceptable_content_types = null;
	protected array|null $acceptable_encodings = null;
	protected array|null $acceptable_locales = null;

	// -----------------

	public function __construct(mixed $data = [])
	{
		$this->headers = new RequestHeaders(array_change_key_case($data['headers']));
		$this->url = UrlObject::fromString($this->getFullUrlString());

		$this->body = $data['body'];
		$this->post = new RequestData($data['post']);
		$this->query = new RequestData($data['query']);
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
		return $this->url->copy()->parameters([]);
	}

	public function scheme(): Scheme
	{
		return Scheme::tryFrom(Framework::environment()->server()->get('REQUEST_SCHEME', 'https')) ?? Scheme::Https;
	}

	public function port(): int
	{
		return (int) Framework::environment()->server()->get('SERVER_PORT');
	}

	public function path(): string
	{
		return Str::before(Framework::environment()->server()->get('REQUEST_URI'), '?');
	}

	public function pathMatchesPattern(string $pattern): bool
	{
		$pattern = preg_replace('/\*/', '(.+)', $pattern);
		$pattern = preg_replace('/{(.*?)}/', '(.+)', $pattern);
		return preg_match_all('#^' . $pattern . '$#', $this->path()) === 1;
	}

	public function queryString(): string
	{
		return Url::arrayToQuery($this->url()->config()->parameters);
	}

	// -----------------

	public function targetHost(): string
	{
		return Framework::environment()->server()->get('HTTP_HOST');
	}

	public function remoteHost(): string
	{
		return Framework::environment()->server()->get('REMOTE_HOST');
	}

	// -----------------

	public function realMethod(): RequestMethod
	{
		$method = Framework::environment()->server()->get('REQUEST_METHOD');
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
		return $this->scheme() === Scheme::Https || Framework::environment()->server()->get('HTTPS') === 'on';
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

	public function time(): Moment
	{
		return new Moment(Framework::environment()->server()->get('REQUEST_TIME_FLOAT'));
	}

	public function protocol(): string
	{
		return Framework::environment()->server()->get('SERVER_PROTOCOL');
	}

	public function format(): string|null
	{
		return $this->headers->get('Content-Type');
	}

	public function cacheControl(): string|null
	{
		return $this->headers->get('Cache-Control');
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
			default => Framework::environment()->server()->get('REMOTE_ADDR'),
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

		return Http::getApproximateDeviceFromUserAgent($this->headers->get('User-Agent'));
	}

	public function locale(): string
	{
		$accepts = $this->getAcceptableLocales();
		return array_key_first($accepts);
	}

	// -----------------

	public function hasCredentials(): bool
	{
		return $this->username() !== null && $this->password() !== null;
	}

	public function username(): string|null
	{
		$username = Framework::environment()->server()->get('PHP_AUTH_USER');
		return Str::length($username) > 0 ? $username : null;
	}

	public function password(): string|null
	{
		$password = Framework::environment()->server()->get('PHP_AUTH_PW');
		return Str::length($password) > 0 ? $password : null;
	}

	public function authType(): string|null
	{
		$value = Str::before($this->headers->string('Authorization'), ' ');
		return mb_strlen($value) > 0 ? $value : null;
	}

	public function authToken(): string|null
	{
		$value = Str::after($this->headers->string('Authorization'), ' ');
		return mb_strlen($value) > 0 ? $value : null;
	}

	public function bearerToken(): string|null
	{
		return $this->authType() === 'Bearer' ? $this->authToken() : null;
	}

	// -----------------

	// TODO: Cookies

	// -----------------

	public function encoding(): string|null
	{
		$accepts = $this->getAcceptableEncodings();
		return !empty($accepts) ? array_key_first($accepts) : null;
	}

	public function expects(): string|null
	{
		$accepts = $this->getAcceptableContentTypes();
		return !empty($accepts) ? array_key_first($accepts) : null;
	}

	// -----------------

	public function prefers(string|array $content_types): string|null
	{
		$accepts = $this->getAcceptableContentTypes();
		if (empty($accepts)) {
			return null;
		}

		$types = is_array($content_types) ? $content_types : [$content_types];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains(['*/*', '*'], $accept)) {
				return $types[0];
			}

			foreach ($types as $type) {
				if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
					return $type;
				}
			}
		}

		return null;
	}

	public function prefersEncoding(string|array $encodings): string|null
	{
		$accepts = $this->getAcceptableEncodings();
		if (empty($accepts)) {
			return null;
		}

		$encodings = is_array($encodings) ? $encodings : [$encodings];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains($encodings, $accept)) {
				return $accept;
			}
		}
		return null;
	}

	public function prefersLocale(string|array $locales, string|null $default = null): string|null
	{
		$accepts = $this->getAcceptableLocales();
		if (empty($accepts)) {
			return $default;
		}

		$locales = is_array($locales) ? $locales : [$locales];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains($locales, $accept)) {
				return $accept;
			}
		}
		return $default;
	}

	public function prefersFresh(): bool
	{
		return $this->cacheControl() === 'no-cache';
	}

	public function prefersSafeContent(): bool
	{
		return $this->headers->text('Prefer')->containsAny(['Safe', 'safe']);
	}

	// -----------------

	public function accepts(string|array $content_types): bool
	{
		return $this->prefers($content_types) !== null;
	}

	public function acceptsAnyContentType(): bool
	{
		return $this->accepts('*/*');
	}

	public function acceptsHtml(): bool
	{
		return $this->accepts('text/html');
	}

	public function acceptsJson(): bool
	{
		return $this->accepts('application/json');
	}

	public function acceptsWebP(): bool
	{
		return $this->accepts('image/webp');
	}

	// -----------------

	public function getAcceptableLocales(): array
	{
		if ($this->acceptable_locales !== null) {
			return $this->acceptable_locales;
		}

		$locales = $this->acceptHeaderToArray($this->headers->get('Accept-Language'));
		if (empty($locales)) {
			return [Localization::getDefaultLocale() => 1.0];
		}

		$normalized = [];
		foreach ($locales as $locale => $quality) {
			$locale = mb_strlen($locale) === 2 ? $locale.'_'.strtoupper($locale) : $locale;
			$locale = str_replace('-', '_', $locale);
			if (!isset($locales[$locale])) {
				$normalized[$locale] = $quality;
			}
		}

		return $this->acceptable_locales = $normalized;
	}

	public function getAcceptableEncodings(): array
	{
		if ($this->acceptable_encodings !== null) {
			return $this->acceptable_encodings;
		}

		$encodings = $this->acceptHeaderToArray($this->headers->get('Accept-Encoding'));
		return $this->acceptable_encodings = $encodings;
	}

	public function getAcceptableContentTypes(): array
	{
		if ($this->acceptable_content_types !== null) {
			return $this->acceptable_content_types;
		}

		$types = $this->acceptHeaderToArray($this->headers->get('Accept'));
		return $this->acceptable_content_types = $types;
	}

	// -----------------

	protected function getFullUrlString(): string
	{
		$scheme = Framework::environment()->server()->get('REQUEST_SCHEME', 'https');
		$host = Framework::environment()->server()->get('HTTP_HOST', 'localhost');
		$port = Framework::environment()->server()->get('SERVER_PORT', '80');
		$path = Str::before(Framework::environment()->server()->get('REQUEST_URI'), '?');
		$query = Framework::environment()->server()->get('QUERY_STRING');

		return sprintf('%s://%s:%s%s', $scheme, $host, $port, $path. (strlen($query) > 0 ? '?' : '') .$query);
	}

	protected function matchesType(string $actual, string $type): bool
	{
		if ($actual === $type) {
			return true;
		} else {
			$actual = explode('/', $actual);
			$type = explode('/', $type);
			if ($actual[0] !== $type[0]) {
				return false;
			}
			return $actual[1] === '*';
		}
	}

	// -----------------

	protected function acceptHeaderToArray(string|null $header): array
	{
		$header = trim($header ?? '');
		if (mb_strlen($header) === 0) {
			return [];
		}
		return array_reduce(explode(',', $header),
			function ($carry, $element) {
				$type = Str::before($element, ';');
				$quality = str_contains($element, ';q=') ? Str::afterLast($element, ';q=') : 1.00;
				$carry[trim($type)] = (float) $quality;
				return $carry;
			},[]
		);
	}

}