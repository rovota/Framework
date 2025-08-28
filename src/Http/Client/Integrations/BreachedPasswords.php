<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Client\Integrations;

use Rovota\Framework\Facades\Http;
use Rovota\Framework\Http\Client\Client;
use Rovota\Framework\Support\Str;

/**
 * This integration is made possible by HaveIBeenPwned, a project created by Troy Hunt.
 */
class BreachedPasswords
{

	protected Client $client;

	protected array $results = [];

	// -----------------

	public function __construct(mixed $options = [])
	{
		$this->client = Http::create([
			'url' => 'https://api.pwnedpasswords.com',
			'driver' => 'dynamic',
			'options' => $options,
		]);
	}

	// -----------------

	/**
	 * Provide the SHA1 hash of the password you wish to check.
	 */
	public function has(string $hash): bool
	{
		return $this->appearances($hash) > 0;
	}

	/**
	 * Provide the SHA1 hash of the password you wish to check.
	 */
	public function appearances(string $hash): int
	{
		$hash = strtoupper($hash);
		$entries = $this->getResultsForHash($hash);

		if (array_key_exists($hash, $entries)) {
			return $entries[$hash];
		}

		return 0;
	}

	// -----------------

	public function clearCache(): static
	{
		$this->results = [];
		return $this;
	}

	// -----------------

	public function getResultsForHash(string $hash): array
	{
		return $this->getResultsForPrefix(Str::limit(strtoupper($hash), 5));
	}

	private function getResultsForPrefix(string $prefix): array
	{
		if (array_key_exists($prefix, $this->results)) {
			return $this->results[$prefix];
		}

		$response = $this->execute($prefix);
		$entries = [];

		if ($response !== null) {
			foreach (preg_split("/((\r?\n)|(\r\n?))/", $response) as $line) {
				[$suffix, $count] = explode(':', $line);
				if ((int)$count > 0) {
					$entries[$prefix . $suffix] = (int)$count;
				}
			}
		}

		$this->results[$prefix] = $entries;

		return $entries;
	}

	private function execute(string $prefix): string|null
	{
		$response = $this->client->get('/range/' . $prefix)->withHeaders(['Add-Padding' => 'true'])->send();

		return $response->failed() ? null : $response->body();
	}

}