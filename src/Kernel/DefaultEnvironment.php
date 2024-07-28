<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Kernel;

use Rovota\Framework\Kernel\Enums\EnvironmentType;
use Rovota\Framework\Support\Str;

class DefaultEnvironment
{

	public Server $server;

	public EnvironmentType $type;

	// -----------------

	public function __construct()
	{
		$this->server = new Server();

		$this->type = $this->getEnvironmentType();
	}

	// -----------------

	public function hasDebugEnabled(): bool
	{
		return getenv('ENABLE_DEBUG') === 'true';
	}

	public function hasLoggingEnabled(): bool
	{
		return getenv('ENABLE_LOGGING') === 'true';
	}

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	// -----------------

	private function getEnvironmentType(): EnvironmentType
	{
		if (is_string(getenv('ENVIRONMENT'))) {
			return EnvironmentType::tryFrom(getenv('ENVIRONMENT')) ?? EnvironmentType::Production;
		}

		$server_name = $this->server->get('server_name');
		$server_address = $this->server->get('server_addr');

		if ($this->isDevelopmentEnvironment($server_name, $server_address)) {
			return EnvironmentType::Development;
		}

		if ($this->isTestEnvironment($server_name)) {
			return EnvironmentType::Testing;
		}

		if ($this->isStagingEnvironment($server_name)) {
			return EnvironmentType::Staging;
		}

		return EnvironmentType::Production;
	}

	// -----------------

	private function isDevelopmentEnvironment(string $name, string $address): bool
	{
		if (Str::containsAny($name, ['dev.', 'local.', 'sandbox.'])) {
			return true;
		}

		if (Str::endsWithAny($name, ['.localhost', '.local', '.dev'])) {
			return true;
		}

		if ($address === '127.0.0.1' || $address === '::1') {
			return true;
		}

		return false;
	}

	private function isTestEnvironment(string $name): bool
	{
		if (Str::containsAny($name, ['test.', 'qa.', 'uat.', 'acceptance.', 'integration.'])) {
			return true;
		}

		if (Str::endsWithAny($name, ['.test', '.example'])) {
			return true;
		}

		return false;
	}

	private function isStagingEnvironment(string $name): bool
	{
		if (Str::containsAny($name, ['stage.', 'staging.', 'prepod.'])) {
			return true;
		}

		return false;
	}

}