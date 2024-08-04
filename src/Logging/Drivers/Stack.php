<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Drivers;

use Rovota\Framework\Logging\ChannelConfig;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Rovota\Framework\Logging\LoggingManager;
use Stringable;

final class Stack implements ChannelInterface
{

	protected string $name;

	protected ChannelConfig $config;

	// -----------------

	public function __construct(string $name, ChannelConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return LoggingManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): ChannelConfig
	{
		return $this->config;
	}

	// -----------------

	public function attach(ChannelInterface|string|array $channel): ChannelInterface
	{
		$current = $this->config->channels;
		$new = is_array($channel) ? $channel : [$channel];

		$this->config->set('channels', array_merge($current, $new));
		return $this;
	}

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->log($level, $message, $context); continue;
			}
			LoggingManager::get($channel)->log($level, $message, $context);
		}
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('debug', $message, $context);
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('info', $message, $context);
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('notice', $message, $context);
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('warning', $message, $context);
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('error', $message, $context);
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('critical', $message, $context);
	}

	public function alert(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('alert', $message, $context);
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		$this->dispatch('emergency', $message, $context);
	}

	// -----------------

	protected function dispatch(string $type, string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->{$type}($message, $context); continue;
			}
			LoggingManager::get($channel)->{$type}($message, $context);
		}
	}

}