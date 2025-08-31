<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Integrations;

use Rovota\Framework\Facades\Http;
use Rovota\Framework\Facades\Log;
use Rovota\Framework\Http\Client\Client;
use Throwable;

/**
 * This integration is made possible by Cloudflare Turnstile.
 */
class Turnstile
{

	protected Client $client;

	protected string|null $secret = null;

	// -----------------

	public function __construct(mixed $options = [])
	{
		$this->client = Http::create([
			'url' => 'https://challenges.cloudflare.com/turnstile/v0',
			'driver' => 'dynamic',
			'options' => $options,
		]);
	}

	// -----------------

	/**
	 * Provide the client-side token included with the form submission.
	 */
	public function verify(string $token): bool
	{
		$response = $this->client->json('/siteverify')->with([
			'secret' => $this->secret ?? getenv('TURNSTILE_SECRET'),
			'response' => $token,
		])->send();

		if ($response->failed()) {
			Log::notice('There was a problem with reaching the Turnstile API', [$response->body()]);
		}

		try {
			return $response->json()['success'] ?? false;
		} catch (Throwable $throwable) {
			Log::notice('There was a problem processing the Turnstile API response.', [$throwable->getMessage()]);
			return false;
		}
	}

	// -----------------

	public function secret(string $secret): static
	{
		$this->secret = trim($secret);
		return $this;
	}

}