<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Casts;

use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Kernel\ExceptionHandler;
use Rovota\Framework\Security\EncryptionManager;
use Throwable;

final class EncryptionCast implements CastInterface
{

	public function supports(mixed $value, array $options): bool
	{
		return is_string($value);
	}

	// -----------------

	public function toRaw(mixed $value, array $options): string|null
	{
		try {
			return EncryptionManager::instance()->getAgent()->encryptString($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function fromRaw(mixed $value, array $options): string|null
	{
		try {
			return EncryptionManager::instance()->getAgent()->decryptString($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

}