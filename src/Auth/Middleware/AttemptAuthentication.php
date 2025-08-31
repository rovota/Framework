<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Middleware;

use Rovota\Framework\Auth\Provider;
use Rovota\Framework\Http\Request\RequestObject;

class AttemptAuthentication
{

	public function handle(RequestObject $request): void
	{
		if ($request->route() !== null) {

			$provider = $request->route()->getAuthProvider();

			if ($provider instanceof Provider) {
				$provider->adapter->initialize();
			}
		}
	}

}