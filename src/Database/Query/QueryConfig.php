<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Rovota\Framework\Database\Enums\TrashMode;
use Rovota\Framework\Database\Model\Model;
use Rovota\Framework\Support\Config;

final class QueryConfig extends Config
{

	public string|null $table {
		get => $this->get('table');
		set {
			if ($value === null) {
				$this->remove('table');
			}
			$this->set('table', $value);
		}
	}

	// -----------------

	public Model|null $model {
		get => $this->get('model');
		set {
			if ($value === null) {
				$this->remove('model');
			} else {
				if (is_string($value)) {
					$value = new $value();
				}

				$this->set('model', $value);
				$this->set('table', $value->config->table);
			}
		}
	}

	// -----------------

	public TrashMode $trash_mode {
		get => TrashMode::tryFrom($this->int('trash_mode'));
		set {
			$this->set('trash_mode', $value->value);
		}
	}

}