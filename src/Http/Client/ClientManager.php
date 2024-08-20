<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client;

use Rovota\Framework\Kernel\ServiceProvider;

/**
 * @internal
 */
final class ClientManager extends ServiceProvider
{

	public function createClient(mixed $options = []): Client
	{
		return new Client($options);
	}

}