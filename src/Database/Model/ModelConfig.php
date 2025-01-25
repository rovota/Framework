<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model;

use Rovota\Framework\Database\ConnectionManager;
use Rovota\Framework\Facades\DB;
use Rovota\Framework\Support\Arr;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

final class ModelConfig extends Config
{

	protected Model $model;

	// -----------------

	public function attachModelReference(Model $model): void
	{
		$this->model = $model;
	}

	// -----------------

	public string $connection {
		get => $this->string('connection', ConnectionManager::instance()->getDefault());
		set {
			if (ConnectionManager::instance()->has($value)) {
				$this->set('connection', trim($value));
			}
		}
	}

	public string $table {
		get => $this->string('table', $this->getTableNameFromClass());
		set {
			if (DB::connection($this->connection)->handler->hasTable($value)) {
				$this->set('table', trim($value));
			}
		}
	}
	
	public string $primary_key {
		get => $this->string('primary_key', 'id');
		set {
			$this->set('primary_key', trim($value));
		}
	}

	// -----------------

	public bool $stored {
		get => $this->bool('stored');
		set {
			$this->set('stored', $value);
		}
	}

	public bool $auto_increment {
		get => $this->bool('auto_increment', true);
		set {
			$this->set('auto_increment', $value);
		}
	}

	public bool $manage_timestamps {
		get => $this->bool('manage_timestamps', true);
		set {
			$this->set('manage_timestamps', $value);
		}
	}

	public bool $composites {
		get => $this->bool('composites', true);
		set {
			$this->set('composites', $value);
		}
	}

	// -----------------

	public string $query_order_column {
		get => $this->string('query_order_column', $this->model::CREATED_COLUMN);
		set {
			$this->set('query_order_column', trim($value));
		}
	}

	// -----------------

	protected function getTableNameFromClass(): string
	{
		$normalized = $this->text($this->model::class)->afterLast('\\')->snake();
		$sections = $normalized->explode('_');

		foreach ($sections as $key => $section) {
			$sections[$key] = Str::singular($section);
		}

		$last_item = Arr::last($sections);
		$combined = implode('_', $sections);

		return str_replace($last_item, Str::plural($last_item), $combined);
	}

}