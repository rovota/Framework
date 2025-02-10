<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Traits;

use Rovota\Framework\Database\Enums\TrashMode;
use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Database\Query\Extensions\SelectQuery;

trait TrashQueryConstraints
{

	public function withoutTrashed(): SelectQuery
	{
		$this->config->trash_mode = TrashMode::None;
		return $this;
	}

	public function onlyTrashed(): SelectQuery
	{
		$this->config->trash_mode = TrashMode::Only;
		return $this;
	}

	public function withTrashed(): SelectQuery
	{
		$this->config->trash_mode = TrashMode::Mixed;
		return $this;
	}

	// -----------------

	protected function applyTrashConstraints(): void
	{
		if ($this->config->model instanceof ModelInterface && defined($this->config->model::class . '::TRASHED_COLUMN')) {
			if ($this->config->trash_mode === TrashMode::None) {
				$this->whereNull($this->config->model::TRASHED_COLUMN);
			}
			if ($this->config->trash_mode === TrashMode::Only) {
				$this->whereNotNull($this->config->model::TRASHED_COLUMN);
			}
		}
	}

}