<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation;

use Rovota\Framework\Database\ConnectionManager;
use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Facades\DB;
use Rovota\Framework\Http\Request\UploadedFile;
use Rovota\Framework\Storage\Contents\File;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Str;

final class ValidationTools
{

	protected static array $mime_types = [];
	protected static array $mime_types_reverse = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function processDatabaseOptions(array $options): array
	{
		$config = [
			'connection' => ConnectionManager::instance()->getDefault(),
			'column' => 'id',
		];

		if (str_contains($options[0], '\\')) {
			/** @var Model $model */
			$model = new $options[0]();
			$config['connection'] = $model->config->connection;
			$config['table'] = $model->config->table;
			$config['column'] = $model->config->primary_key;
		} else if (str_contains($options[0], '.')) {
			$location = explode('.', $options[0]);
			$config['connection'] = $location[0];
			$config['table'] = $location[1];
		} else {
			$config['table'] = $options[0];
		}

		if (isset($options[1]) && is_string($options[1])) {
			$config['column'] = $options[1];
		}

		return $config;
	}

	/**
	 * @internal
	 */
	public static function getOccurrences(array $config, mixed $data): int
	{
		return DB::connection($config['connection'])
			->select()
			->from($config['table'])
			->where($config['column'], $data)
			->count();
	}

	// -----------------

	public static function getSize(mixed $data): int|float
	{
		return match (true) {
			$data instanceof File => round($data->properties->size / 1024), // Bytes to Kilobytes
			$data instanceof UploadedFile => round($data->properties->size / 1024), // Bytes to Kilobytes
			is_int($data), is_float($data) => $data,
			is_numeric($data), is_string($data) => Str::length($data),
			is_array($data) => count($data),
			default => 0
		};
	}

	// -----------------

	public static function mimeTypeExists(string $type): bool
	{
		self::loadMimeTypes();
		return isset(self::$mime_types[$type]);
	}

	public static function mimeTypeExtensions(string $type): array
	{
		self::loadMimeTypes();
		return self::$mime_types[$type] ?? [];
	}

	public static function extensionExists(string $extension): bool
	{
		self::loadMimeTypesReversed();
		return isset(self::$mime_types_reverse[$extension]);
	}

	public static function extensionMimeTypes(string $extension): array
	{
		self::loadMimeTypesReversed();
		return self::$mime_types_reverse[$extension] ?? [];
	}

	// -----------------

	public static function sanitizeSelect(string $option, array $options, string $fallback): string
	{
		return Arr::contains($options, $option) ? $option : $fallback;
	}

	public static function sanitizeExtension(string $type, string $extension): string|null
	{
		$extensions = self::mimeTypeExtensions($type);

		if (Arr::contains($extensions, $extension)) {
			return $extension;
		}

		return $extensions[0] ?? null;
	}

	public static function sanitizeMimeType(string $extension, string $type): string|null
	{
		$mime_types = self::extensionMimeTypes($extension);

		if (Arr::contains($mime_types, $type)) {
			return $type;
		}

		return $mime_types[0] ?? null;
	}

	// -----------------

	protected static function loadMimeTypes(): void
	{
		if (empty(self::$mime_types)) {
			self::$mime_types = include 'sources/mime_types.php';
		}
	}

	protected static function loadMimeTypesReversed(): void
	{
		if (empty(self::$mime_types_reverse)) {
			self::$mime_types_reverse = include 'sources/mime_types_reverse.php';
		}
	}

}