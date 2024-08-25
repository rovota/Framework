<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Facades;

use Closure;
use Rovota\Framework\Http\Enums\RequestMethod;
use Rovota\Framework\Http\Request\RequestManager;
use Rovota\Framework\Http\Request\RequestObject;
use Rovota\Framework\Routing\Enums\Scheme;
use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Support\Facade;
use Rovota\Framework\Support\Moment;

/**
 * @method static RequestObject current()
 *
 * @method static bool hasExposedCredentials()
 * @method static bool hasPrivacyControl()
 * @method static bool hasDoNotTrack()
 *
 * @method static UrlObject url()
 * @method static UrlObject urlWithoutParameters()
 * @method static Scheme scheme()
 * @method static int port()
 * @method static string path()
 * @method static bool pathMatchesPattern(string $pattern)
 * @method static string queryString()
 *
 * @method static string targetHost()
 * @method static string remoteHost()
 *
 * @method static RequestMethod realMethod()
 * @method static RequestMethod method()
 * @method static bool isMethod(RequestMethod|string $method)
 * @method static bool isPost()
 *
 * @method static bool isSecure()
 * @method static bool isProxy()
 * @method static bool isXmlHttp()
 * @method static bool isJson()
 *
 * @method static Moment time()
 * @method static string protocol()
 * @method static string|null format()
 * @method static string|null cacheControl()
 *
 * @method static string|null referrer()
 * @method static string|null useragent()
 * @method static string|null country()
 * @method static string ip()
 * @method static string device()
 * @method static string platform()
 * @method static string locale()
 *
 * @method static bool hasCredentials()
 * @method static string|null username()
 * @method static string|null password()
 * @method static string|null authType()
 * @method static string|null authToken()
 * @method static string|null bearerToken()
 *
 * @method static string|null encoding()
 * @method static string|null expects()
 *
 * @method static string|null prefers(string|array $content_types)
 * @method static string|null prefersEncoding(string|array $encodings)
 * @method static string|null prefersLocale(string|array $locales, string|null $default = null)
 * @method static string|null prefersFresh()
 * @method static string|null prefersSafeContent()
 *
 * @method static bool accepts(string|array $content_types)
 * @method static bool acceptsAnyContentType()
 * @method static bool acceptsHtml()
 * @method static bool acceptsJson()
 * @method static bool acceptsWebP()
 */
final class Request extends Facade
{

	public static function service(): RequestManager
	{
		return parent::service();
	}

	// -----------------

	protected static function getFacadeTarget(): string
	{
		return RequestManager::class;
	}

	protected static function getMethodTarget(string $method): Closure|string
	{
		return match ($method) {
			'current' => 'getCurrent',
			default => function (RequestManager $instance, string $method, array $parameters = []) {
				return $instance->getCurrent()->$method(...$parameters);
			},
		};
	}

}