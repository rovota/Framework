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

	protected Server $server;

	protected EnvironmentType $type;

	// -----------------

	public function __construct()
	{
		$this->server = new Server();
		$this->type = $this->getEnvironmentType();
	}

	// -----------------

	public function server(): Server
	{
		return $this->server;
	}

	public function type(): EnvironmentType
	{
		return $this->type;
	}

	// -----------------

	public function isLocal(): bool
	{
		return $this->type === EnvironmentType::Development;
	}

	public function isTestable(): bool
	{
		return $this->type === EnvironmentType::Testing;
	}

	public function isStaged(): bool
	{
		return $this->type === EnvironmentType::Staging;
	}

	public function isProduction(): bool
	{
		return $this->type === EnvironmentType::Production;
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

	public function cookieDomain(): string
	{
		return $_SERVER['SERVER_NAME'];
	}

	// -----------------

	public function services(): array
	{
		return [
			// Foundation
			'registry' => RegistryManager::class,
		];
	}

	// -----------------

	// TODO: methods like authProviders() and libraries()
	// For example, Environment::authProviders() returns an array with auth provider classes/config.
	// And Environment::libraries() returns an array of library classes to call a load() method on.

	// -----------------

	protected function detectsDevelopmentEnvironment(string $name, string $address): bool
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

	protected function detectsTestEnvironment(string $name): bool
	{
		if (Str::containsAny($name, ['test.', 'qa.', 'uat.', 'acceptance.', 'integration.'])) {
			return true;
		}

		if (Str::endsWithAny($name, ['.test', '.example'])) {
			return true;
		}

		return false;
	}

	protected function detectsStagingEnvironment(string $name): bool
	{
		if (Str::containsAny($name, ['stage.', 'staging.', 'prepod.'])) {
			return true;
		}

		return false;
	}

	// -----------------

	private function getEnvironmentType(): EnvironmentType
	{
		if (is_string(getenv('ENVIRONMENT'))) {
			return EnvironmentType::tryFrom(getenv('ENVIRONMENT')) ?? EnvironmentType::Production;
		}

		$server_name = $this->server->get('server_name');
		$server_address = $this->server->get('server_addr');

		if ($this->detectsDevelopmentEnvironment($server_name, $server_address)) {
			return EnvironmentType::Development;
		}

		if ($this->detectsTestEnvironment($server_name)) {
			return EnvironmentType::Testing;
		}

		if ($this->detectsStagingEnvironment($server_name)) {
			return EnvironmentType::Staging;
		}

		return EnvironmentType::Production;
	}

}