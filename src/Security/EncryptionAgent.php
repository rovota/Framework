<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Security;

use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Security\Exceptions\EncryptionException;
use Rovota\Framework\Security\Exceptions\IncorrectKeyException;
use Rovota\Framework\Security\Exceptions\PayloadException;
use Rovota\Framework\Security\Exceptions\UnsupportedCipherException;
use Throwable;

final class EncryptionAgent
{
	protected string $key;

	protected string $cipher;

	protected array $supported_ciphers = [
		'AES-128-CBC' => ['size' => 16, 'AEAD' => false],
		'AES-256-CBC' => ['size' => 32, 'AEAD' => false],
		'AES-128-GCM' => ['size' => 16, 'AEAD' => true],
		'AES-256-GCM' => ['size' => 32, 'AEAD' => true],
	];

	// -----------------

	/**
	 * @throws IncorrectKeyException
	 */
	public function __construct(string $key, string $cipher)
	{
		if ($this->supports($cipher, $key) === false) {
			throw new IncorrectKeyException("Unsupported cipher or incorrect key length.");
		}

		$this->key = $key;
		$this->cipher = $cipher;
	}

	// -----------------

	public function getKey(): string
	{
		return $this->key;
	}

	public function getKeyEncoded(): string
	{
		return base64_encode($this->key);
	}

	public function getCipher(): string
	{
		return $this->cipher;
	}

	// -----------------

	public function supports(string $cipher, string|null $key = null): bool
	{
		$cipher = strtoupper($cipher);

		if (!isset($this->supported_ciphers[$cipher])) {
			return false;
		}

		if ($key !== null) {
			$key_length = mb_strlen($key, '8bit');
			return $key_length === $this->supported_ciphers[$cipher]['size'];
		}

		return true;
	}

	// -----------------

	/**
	 * @throws EncryptionException
	 */
	public function encrypt(mixed $value, bool $serialize = true): string
	{
		$iv_length = openssl_cipher_iv_length($this->cipher);
		$iv = openssl_random_pseudo_bytes($iv_length);
		$tag = '';

		$data = $serialize ? serialize($value) : $value;

		if ($this->supported_ciphers[$this->cipher]['AEAD']) {
			$value = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv, $tag);
		} else {
			$value = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
		}

		if ($value === false) {
			throw new EncryptionException('The given data could not be encrypted.');
		}

		$iv = base64_encode($iv);
		$tag = base64_encode($tag);

		$mac = $this->supported_ciphers[$this->cipher]['AEAD'] ? '' : $this->hash($iv, $value);
		$json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new EncryptionException('The given data could not be encrypted.');
		}

		return base64_encode($json);
	}

	/**
	 * @throws PayloadException
	 */
	public function decrypt(string $payload, bool $deserialize = true): mixed
	{
		$payload = $this->getJsonPayload($payload);

		$iv = base64_decode($payload['iv']);
		$tag = empty($payload['tag']) ? null : base64_decode($payload['tag']);

		if ($this->supported_ciphers[$this->cipher]['AEAD'] && mb_strlen($tag) === 32) {
			throw new PayloadException('The given payload could not be decrypted.');
		}

		$decrypted = openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv, $tag ?? '');

		if ($decrypted === false) {
			throw new PayloadException('The given payload could not be decrypted.');
		}

		return $deserialize ? unserialize($decrypted) : $decrypted;
	}

	// -----------------

	/**
	 * @throws UnsupportedCipherException
	 */
	public function generateKey(string $cipher, bool $encode = false): string
	{
		if ($this->supports($cipher) === false) {
			throw new UnsupportedCipherException("The provided cipher is not supported.");
		}

		$iteration = 0; $bytes = '';

		while ($iteration < 1) {
			try {
				$bytes = random_bytes($this->supported_ciphers[strtoupper($cipher)]['size'] ?? 32);
			} catch (Throwable $throwable) {
				ExceptionHandler::logThrowable($throwable);
			}
			$iteration++;
		}

		return $encode ? base64_encode($bytes) : $bytes;
	}

	// -----------------

	protected function hash(string $iv, mixed $value): string
	{
		return hash_hmac('sha256', $iv.$value, $this->key);
	}

	/**
	 * @throws PayloadException
	 */
	protected function getJsonPayload(string $payload): array
	{
		$payload = json_decode(base64_decode($payload), true);

		if (!$this->isValidPayload($payload)) {
			throw new PayloadException('The given payload is invalid.');
		}

		if (!$this->supported_ciphers[$this->cipher]['AEAD'] && !$this->isValidMac($payload)) {
			throw new PayloadException('An invalid MAC has been provided.');
		}

		return $payload;
	}

	protected function isValidPayload(mixed $payload): bool
	{
		if (is_array($payload) === false) {
			return false;
		}

		if (isset($payload['iv'], $payload['value'], $payload['mac']) === false) {
			return false;
		}

		$iv = base64_decode($payload['iv'], true) ?? '';
		$iv_length = openssl_cipher_iv_length($this->cipher);

		if (strlen($iv) !== $iv_length) {
			return false;
		}

		return true;
	}

	protected function isValidMac(array $payload): bool
	{
		return hash_equals($this->hash($payload['iv'], $payload['value']), $payload['mac']);
	}

}