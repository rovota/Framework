<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http;

use Rovota\Framework\Support\Config;

/**
 * @property-read array $headers
 */
class ResponseConfig extends Config
{

	protected function getHeaders(): array
	{
		return $this->array('headers', [
			'X-Content-Type-Options' => 'nosniff',
			'Referrer-Policy' => 'same-origin',
			'Cross-Origin-Opener-Policy' => 'same-origin',
			'Cross-Origin-Resource-Policy' => 'cross-origin',
			'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
			'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), usb=()',
			'Vary' => 'Origin, User-Agent',
		]);
	}

}