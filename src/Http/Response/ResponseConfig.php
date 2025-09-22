<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Response;

use Rovota\Framework\Support\Config;

class ResponseConfig extends Config
{

	public array $headers {
		get => $this->array('headers', [
			'X-Frame-Options' => 'SAMEORIGIN',
			'X-Content-Type-Options' => 'nosniff',
			'Referrer-Policy' => 'same-origin',
			'Cross-Origin-Opener-Policy' => 'same-origin',
			'Cross-Origin-Resource-Policy' => 'cross-origin',
			'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
			'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), usb=()',
			'Vary' => 'Origin, User-Agent',
		]);
		set {
			$this->set('headers', $value);
		}
	}

}