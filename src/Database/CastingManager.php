<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database;

use Rovota\Framework\Database\Casts\ArrayCast;
use Rovota\Framework\Database\Casts\BooleanCast;
use Rovota\Framework\Database\Casts\DateTimeCast;
use Rovota\Framework\Database\Casts\EncryptionCast;
use Rovota\Framework\Database\Casts\EncryptionStringCast;
use Rovota\Framework\Database\Casts\EnumCast;
use Rovota\Framework\Database\Casts\FloatCast;
use Rovota\Framework\Database\Casts\IntegerCast;
use Rovota\Framework\Database\Casts\JsonCast;
use Rovota\Framework\Database\Casts\MomentCast;
use Rovota\Framework\Database\Casts\ObjectCast;
use Rovota\Framework\Database\Casts\SerialCast;
use Rovota\Framework\Database\Casts\StringCast;
use Rovota\Framework\Database\Casts\TextCast;
use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class CastingManager extends ServiceProvider
{

	/**
	 * @var array<string, CastInterface>
	 */
	protected array $casts = [];

	// -----------------

	public function __construct()
	{
		$this->registerDefaultCasts();
	}

	// -----------------

	public function castToRaw(mixed $value, string|array $options): mixed
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = $this->getCast(array_shift($options));

		if ($cast instanceof CastInterface && $cast->supports($value, $options)) {
			return $cast->toRaw($value, $options);
		}

		return $value;
	}

	public function castToRawAutomatic(mixed $value): mixed
	{
		foreach ($this->casts as $cast) {
			if ($cast->supports($value, [])) {
				return $cast->toRaw($value, []);
			}
		}

		return $value;
	}

	public function castFromRaw(mixed $value, string|array $options): mixed
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = $this->getCast(array_shift($options));

		if ($cast instanceof CastInterface) {
			return $cast->fromRaw($value, $options);
		}

		return $value;
	}

	// -----------------

	public function isAllowedValueForCast(mixed $value, string|array $options): bool
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = $this->getCast(array_shift($options));
		if ($cast instanceof CastInterface && $cast->supports($value, $options)) {
			return true;
		}

		return false;
	}

	// -----------------

	public function hasCast(string $name): bool
	{
		return isset($this->casts[$name]);
	}

	public function addCast(CastInterface $cast, string|null $name = null): void
	{
		$this->casts[$name ?? Str::random(20)] = $cast;
	}

	public function getCast(string $name): CastInterface|null
	{
		return $this->casts[$name] ?? null;
	}

	// -----------------

	protected function registerDefaultCasts(): void
	{
		$this->addCast(new ArrayCast(), 'array');
		$this->addCast(new BooleanCast(), 'bool');
		$this->addCast(new DateTimeCast(), 'datetime');
		$this->addCast(new EnumCast(), 'enum');
		$this->addCast(new FloatCast(), 'float');
		$this->addCast(new IntegerCast(), 'int');
		$this->addCast(new StringCast(), 'string');
		$this->addCast(new ObjectCast(), 'object');

		$this->addCast(new TextCast(), 'text');
		$this->addCast(new MomentCast(), 'moment');
		// TODO: ModelCast

		$this->addCast(new JsonCast(), 'json');
		$this->addCast(new EncryptionCast(), 'encrypted');
		$this->addCast(new EncryptionStringCast(), 'encrypted_string');
		$this->addCast(new SerialCast(), 'serial');
	}

}