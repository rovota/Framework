<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Middleware;

use Rovota\Framework\Auth\Interfaces\ProviderInterface;
use Rovota\Framework\Http\Request\RequestObject;

class AttemptAuthentication
{

	public function handle(RequestObject $request): void
	{
		if ($request->route() !== null) {

			$provider = $request->route()->getAuthProvider();

			if ($provider instanceof ProviderInterface) {
				$provider->adapter->initialize();
			}
		}
	}

}