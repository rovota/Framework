<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging;

use Monolog\Level;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Text;

class LogEntry
{

	protected string $raw;

	// -----------------

	public Moment $moment;
	public Channel $channel;
	public Level $level;
	public string $message;

	public array $context;
	public array $trace;

	// -----------------

	public function __construct(string $raw)
	{
		$this->raw = Str::remove($raw, ['[]']);

		$this->moment = $this->extractMoment();
		$this->channel = $this->extractChannel();
		$this->level = $this->extractLevel();
		$this->message = $this->extractMessage();

		$this->context = $this->extractContext();
		$this->trace = $this->extractTrace();
	}

	// -----------------

	protected function extractMoment(): Moment
	{
		$extract = Text::from($this->raw)->after('[')->before(']')->toString();
		return Moment::parse($extract);
	}

	protected function extractChannel(): Channel
	{
		$extract = Text::from($this->raw)->after('] ')->before(': ')->before('.')->toString();
		return LoggingManager::instance()->get($extract);
	}

	protected function extractLevel(): Level
	{
		$extract = Text::from($this->raw)->after('] ')->before(': ')->after('.')->toString();
		return Level::fromName($extract);
	}

	protected function extractMessage(): string
	{
		return Text::from($this->raw)->after(': ')->before(' {"')->before(' ["')->toString();
	}

	// -----------------

	protected function extractContext(): array
	{
		if (!Str::contains($this->raw, ' {"')) {
			return [];
		}

		$extract = Text::from($this->raw)->after(' {')->before('} [')->toString();
		$extract = '{' . $extract . '}';

		return json_decode($extract, true);
	}

	protected function extractTrace(): array
	{
		$extract = Text::from($this->raw)->after(' ["')->before('] ')->toString();
		$parts = explode(',', $extract);

		foreach ($parts as $key => $part) {
			$parts[$key] = trim($part, '"');
		}

		return $parts;
	}

}