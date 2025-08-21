<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security\Middleware;

use Rovota\Framework\Facades\Cookie;
use Rovota\Framework\Facades\Response;
use Rovota\Framework\Http\Enums\StatusCode;
use Rovota\Framework\Http\Request\RequestObject;
use Rovota\Framework\Security\CsrfManager;

class CsrfProtection
{
	/**
	 * Reject requests that are using the POST method but do not specify a CSRF token.
	 */
	public function handle(RequestObject $request): void
	{
		$token_name = CsrfManager::getTokenName();
		$token_value = CsrfManager::getToken();

		if ($request->isPost() && $request->get($token_name) !== $token_value) {
			echo response(StatusCode::Forbidden);
			exit;
		}

		if (request()->cookies->missing($token_name)) {
			Response::attachCookie(
				Cookie::create($token_name, $token_value)
			);
		}
	}

}