<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Framework\Http\Client\Integrations\Traits;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Support\Str;
use Throwable;

trait HibpPasswordService
{

	private array $results = [];

	// -----------------

	public function isPasswordBreached(string $password): bool
	{
		return $this->hasPasswordHash(sha1($password));
	}

	// -----------------

	public function hasPasswordHash(string $hash): bool
	{
		return $this->getPasswordHashCount($hash) > 0;
	}

	/**
	 * Provide the SHA1 hash of the password you wish to check.
	 */
	public function getPasswordHashCount(string $hash): int
	{
		$hash = strtoupper($hash);
		$entries = $this->getResultsForHash($hash);

		if (array_key_exists($hash, $entries)) {
			return $entries[$hash];
		}

		return 0;
	}

	// -----------------

	public function getResultsForHash(string $hash): array
	{
		return $this->retrieveResultsForPrefix($this->getPrefixFromHash($hash));
	}

	// -----------------

	private function getPrefixFromHash(string $hash): string
	{
		return Str::limit(strtoupper($hash), 5);
	}

	private function getSuffixFromHash(string $hash): string
	{
		return substr(strtoupper($hash), 5);
	}

	// -----------------

	private function retrieveResultsForPrefix(string $prefix): array
	{
		if (array_key_exists($prefix, $this->results)) {
			return $this->results[$prefix];
		}

		$response = $this->executeRequest($prefix);
		$entries = [];

		if ($response !== null) {
			foreach (preg_split("/((\r?\n)|(\r\n?))/", $response) as $line) {
				[$suffix, $count] = explode(':', $line);
				if ((int) $count > 0) {
					$entries[$prefix.$suffix] = (int) $count;
				}
			}
		}

		$this->results[$prefix] = $entries;

		return $entries;
	}

	private function executeRequest(string $prefix): string|null
	{
		$url = 'api.pwnedpasswords.com/range/'.$prefix;

		try {
			$response = $this->get($url)
				->header('Add-Padding', 'true')
				->connectTimeout(2)
				->execute()
				->content();
		} catch (Throwable $throwable) {
			ExceptionHandler::handleThrowable($throwable);
			return null;
		}

		return $response;
	}

}