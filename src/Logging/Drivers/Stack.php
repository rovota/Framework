<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Drivers;

use Rovota\Framework\Logging\Channel;
use Rovota\Framework\Logging\ChannelConfig;
use Rovota\Framework\Logging\Handlers\StackHandler;
use Rovota\Framework\Logging\LoggingManager;
use Stringable;

final class Stack extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		parent::__construct($name, new StackHandler(), $config);
	}

	// -----------------

	public function attach(Channel|string|array $channel): Channel
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
			if ($channel instanceof Channel) {
				$channel->log($level, $message, $context);
				continue;
			}
			LoggingManager::instance()->get($channel)->log($level, $message, $context);
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
			if ($channel instanceof Channel) {
				$channel->{$type}($message, $context);
				continue;
			}
			LoggingManager::instance()->get($channel)->{$type}($message, $context);
		}
	}

}