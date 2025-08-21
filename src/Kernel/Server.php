<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Conversion\TextConverter;

final class Server
{

	protected array $variables;

	// -----------------

	/**
	 * Returns the name of the operating system, such as "Windows".
	 */
	public string $platform {
		get => trim(str_replace('NT', '', php_uname('s')));
	}

	/**
	 * Returns the release version of the operating system.
	 */
	public string $version {
		get => php_uname('r');
	}

	/**
	 * returns the exact version/build number of the operating system.
	 */
	public string $build {
		get => php_uname('v');
	}

	/**
	 * Returns the (host)name of the server.
	 */
	public string $name {
		get => php_uname('n');
	}

	/**
	 * Returns the type of architecture the server is running on, such as "AMD64".
	 */
	public string $architecture {
		get => php_uname('m');
	}

	/**
	 * Returns the version of PHP the server is running on.
	 */
	public Version $language {
		get => new Version(PHP_VERSION);
	}

	// -----------------

	public int $max_file_size {
		get {
			$post_max = TextConverter::toBytes(ini_get('post_max_size'));
			$upload_max = TextConverter::toBytes(ini_get('upload_max_filesize'));
			return min($post_max ?: PHP_INT_MAX, $upload_max ?: PHP_INT_MAX);
		}
	}

	// -----------------

	public float $memory_allocated {
		get => (float)memory_get_usage(true);
	}

	public float $memory_usage {
		get => (float)memory_get_usage();
	}

	public float $memory_peak_usage {
		get => (float)memory_get_peak_usage();
	}

	// -----------------

	public float $disk_capacity;
	public float $disk_usage;

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

	protected function loadDiskUsageData(): void
	{
		$path = ($this->platform === 'Windows') ? substr(getcwd(), 0, 3) : __DIR__;

		$this->disk_capacity = disk_total_space($path);
		$this->disk_usage = $this->disk_capacity - disk_free_space($path);
	}

}