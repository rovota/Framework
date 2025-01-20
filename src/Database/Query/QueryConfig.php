<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Query;

use Rovota\Framework\Database\Model\Interfaces\ModelInterface;
use Rovota\Framework\Support\Config;

final class QueryConfig extends Config
{

	public string|array|null $table {
		get => $this->get('table');
		set {
			if ($value === null) {
				$this->remove('table');
			}
			$this->set('table', $value);
		}
	}

	// -----------------

	public ModelInterface|null $model {
		get => $this->get('model');
		set {
			if ($value === null) {
				$this->remove('model');
			} else {
				if (is_string($value)) {
					$value = new $value();
				}

				$this->set('model', $value);
				$this->set('table', $this->model->config->table);
			}
		}
	}

}