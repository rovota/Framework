<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Logging\Handlers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Rovota\Framework\Facades\Http;
use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Moment;
use Rovota\Framework\Support\Str;

class DiscordHandler extends AbstractProcessingHandler
{

	private string $endpoint;

	// -----------------

	public function __construct(string $token, string $channel, int|string|Level $level = Level::Debug, bool $bubble = true)
	{
		$this->endpoint = sprintf('https://discord.com/api/webhooks/%s/%s', $channel, $token);

		parent::__construct($level, $bubble);
	}

	// -----------------

	protected function write(LogRecord $record): void
	{
		Http::json($this->endpoint)->with([
			'content' => Moment::create($record->datetime)->toRfc3339String(),
			'embeds' => [$this->createEmbed($record)],
		])->send();
	}

	// -----------------

	private function createEmbed(LogRecord $record): array
	{
		$parameters = [
			'title' => $record->level->name,
			'type' => 'rich',
			'description' => $record->message,
			'timestamp' => $record->datetime->format('c'),
			'color' => hexdec($this->getColorForLevel($record->level)),
			'footer' => [
				'text' => Str::pascal(Framework::environment()->type->label()),
			],
		];

		foreach ($record->context as $key => $value) {
			$parameters['fields'][] = [
				'name' => $key,
				'value' => $value,
				'inline' => true,
			];
		}

		return $parameters;
	}

	private function getColorForLevel(Level $level): string
	{
		return match ($level->name) {
			'Debug' => '666666',
			'Info' => '37B8E1',
			'Notice' => '0D89CF',
			'Warning' => 'F6902D',
			'Error' => 'E54646',
			'Critical' => 'D5351F',
			'Alert' => '08B5AA',
			'Emergency' => 'C566FF',
			default => 'F1F1F1',
		};
	}

}