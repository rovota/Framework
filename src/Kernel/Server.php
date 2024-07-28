<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use PHLAK\SemVer\Exceptions\InvalidVersionException;
use Rovota\Framework\Conversion\TextConverter;

final class Server
{

	protected array $variables;

	protected float $disk_capacity;
	protected float $disk_usage;

	// -----------------

	public function __construct()
	{
		$this->variables = array_change_key_case($_SERVER);

		$this->loadDiskUsageData();
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->variables[$name]);
	}

	public function get(string $name, string|null $default = null): string|null
	{
		$name = mb_strtolower($name, 'UTF-8');
		return $this->variables[$name] ?? $default;
	}

	// -----------------

	/**
	 * Returns the name of the operating system, such as "Windows".
	 */
	public function platform(): string
	{
		return trim(str_replace('NT', '', php_uname('s')));
	}

	/**
	 * Returns the release version of the operating system.
	 */
	public function version(): string
	{
		return php_uname('r');
	}

	/**
	 * returns the exact version/build number of the operating system.
	 */
	public function build(): string
	{
		return php_uname('v');
	}

	/**
	 * Returns the (host)name of the server.
	 */
	public function name(): string
	{
		return php_uname('n');
	}

	/**
	 * Returns the type of architecture the server is running on, such as "AMD64".
	 */
	public function architecture(): string
	{
		return php_uname('m');
	}

	// -----------------

	/**
	 * @throws InvalidVersionException
	 */
	public function zendVersion(): Version
	{
		return new Version(zend_version());
	}

	public function phpVersion(): Version
	{
		return new Version(PHP_VERSION);
	}

	public function extVersion(string $extension): Version|null
	{
		try {
			return new Version(phpversion($extension));
		} catch (InvalidVersionException) {
			return null;
		}
	}

	public function hasExtension(string $extension): bool
	{
		return extension_loaded($extension);
	}

	// -----------------

	public function maxFilesize(): int
	{
		$post_max = TextConverter::toBytes(ini_get('post_max_size'));
		$upload_max = TextConverter::toBytes(ini_get('upload_max_filesize'));

		return min($post_max ?: PHP_INT_MAX, $upload_max ?: PHP_INT_MAX);
	}

	// -----------------

	public function diskCapacity(): float
	{
		return $this->disk_capacity;
	}

	public function diskUsage(): float
	{
		return $this->disk_usage;
	}

	// -----------------

	public function memoryAllocated(): float
	{
		return (float) memory_get_usage(true);
	}

	public function memoryUsage(): float
	{
		return (float) memory_get_usage();
	}

	public function memoryPeakUsage(): float
	{
		return (float) memory_get_peak_usage(true);
	}

	// -----------------

	protected function loadDiskUsageData(): void
	{
		$path = ($this->platform() === 'Windows') ? substr(getcwd(), 0, 3) : '/';

		$this->disk_capacity = disk_total_space($path);
		$this->disk_usage = $this->disk_capacity - disk_free_space($path);
	}

}