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
use Rovota\Framework\Database\Casts\EnumCast;
use Rovota\Framework\Database\Casts\FloatCast;
use Rovota\Framework\Database\Casts\IntegerCast;
use Rovota\Framework\Database\Casts\JsonCast;
use Rovota\Framework\Database\Casts\MomentCast;
use Rovota\Framework\Database\Casts\ObjectCast;
use Rovota\Framework\Database\Casts\SerialCast;
use Rovota\Framework\Database\Casts\StringCast;
use Rovota\Framework\Database\Casts\TextCast;
use Rovota\Framework\Database\Casts\UrlCast;
use Rovota\Framework\Database\Interfaces\CastInterface;
use Rovota\Framework\Kernel\ServiceProvider;
use Rovota\Framework\Structures\Map;
use Rovota\Framework\Support\Str;

/**
 * @internal
 */
final class CastingManager extends ServiceProvider
{

	/**
	 * @var Map<string, CastInterface>
	 */
	protected Map $casts;

	// -----------------

	public function __construct()
	{
		$this->casts = new Map();

		$this->registerDefaultCasts();
	}

	// -----------------

	public function castToRaw(mixed $value, string|array $options): mixed
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = $this->get(array_shift($options));

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

		$cast = $this->get(array_shift($options));

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

		$cast = $this->get(array_shift($options));
		if ($cast instanceof CastInterface && $cast->supports($value, $options)) {
			return true;
		}

		return false;
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->casts[$name]);
	}

	public function add(CastInterface $cast, string|null $name = null): void
	{
		$this->casts[$name ?? Str::random(20)] = $cast;
	}

	public function get(string $name): CastInterface|null
	{
		return $this->casts[$name] ?? null;
	}

	// -----------------

	public function all(): Map
	{
		return $this->casts;
	}

	// -----------------

	protected function registerDefaultCasts(): void
	{
		$this->add(new ArrayCast(), 'array');
		$this->add(new BooleanCast(), 'bool');
		$this->add(new DateTimeCast(), 'datetime');
		$this->add(new EnumCast(), 'enum');
		$this->add(new FloatCast(), 'float');
		$this->add(new IntegerCast(), 'int');
		$this->add(new StringCast(), 'string');
		$this->add(new ObjectCast(), 'object');

		$this->add(new TextCast(), 'text');
		$this->add(new MomentCast(), 'moment');

		$this->add(new JsonCast(), 'json');
		$this->add(new EncryptionCast(), 'encrypted');
		$this->add(new SerialCast(), 'serial');
		$this->add(new UrlCast(), 'url');
	}

}