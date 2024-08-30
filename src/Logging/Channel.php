<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Rovota\Framework\Logging\Interfaces\ChannelInterface;
use Stringable;

abstract class Channel implements ChannelInterface
{

	protected string $name;

	protected ChannelConfig $config;
	protected HandlerInterface $handler;
	protected Logger $logger;

	// -----------------

	public function __construct(string $name, HandlerInterface $handler, ChannelConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
		$this->handler = $handler;

		$this->logger = new Logger($name);
		$this->logger->pushHandler($handler);
		$this->logger->pushProcessor(new PsrLogMessageProcessor());
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function isDefault(): bool
	{
		return LoggingManager::instance()->getDefault() === $this->name;
	}

	// -----------------

	public function getName(): string
	{
		return $this->name;
	}

	public function getConfig(): ChannelConfig
	{
		return $this->config;
	}

	// -----------------

	public function attach(ChannelInterface|string|array $channel): ChannelInterface
	{
		return LoggingManager::instance()->createStack([$this])->attach($channel);
	}

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		$this->logger->log($level, $message, $context);
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		$this->logger->debug($message, $context);
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		$this->logger->info($message, $context);
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		$this->logger->notice($message, $context);
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		$this->logger->warning($message, $context);
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		$this->logger->error($message, $context);
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		$this->logger->critical($message, $context);
	}

	public function alert(string|Stringable $message, array $context = []): void
	{
		$this->logger->alert($message, $context);
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		$this->logger->emergency($message, $context);
	}

}