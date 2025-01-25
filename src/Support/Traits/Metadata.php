<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Traits;

use Rovota\Framework\Database\Model\ModelConfig;
use Rovota\Framework\Structures\Basket;
use Rovota\Framework\Support\Models\MetaEntity;
use Rovota\Framework\Support\Str;

/**
 * @property ModelConfig $config
 */
trait Metadata
{

	protected Basket|null $meta_entities = null;

	protected MetaEntity|string $meta_model {
		get => Str::singular($this::class).'Meta';
	}

	protected string $meta_foreign_key {
		get => Str::singular($this->config->table).'_id';
	}

	// -----------------

	/**
	 * @var Basket<string, MetaEntity>
	 */
	public Basket $metadata {
		get {
			if ($this->meta_entities === null) {
				$this->loadMetadata();
			}

			return $this->meta_entities;
		}
		set {
			$this->meta_entities = $value;
		}
	}

	// -----------------

	public function meta(string $name, mixed $default = null): mixed
	{
		return $this->metadata->get($name)?->value ?? $default;
	}

	// -----------------

	public function setMeta(string $name, mixed $value, bool $cleanup = true): bool
	{
		/**
		 * @var MetaEntity $model
		 */

		if ($this->metadata->has($name)) {
			$model = $this->metadata->get($name);

			if ($cleanup && $value === null) {
				if ($model->destroy()) {
					$this->metadata->remove($name);
					return true;
				}
			} else {
				$model->value = $value;
				return $model->save();
			}
		}

		if ($cleanup && $value === null) {
			return true;
		}

		$model = new $this->meta_model();
		$model->{$this->meta_foreign_key} = $this->{$this->config->primary_key};
		$model->name = $name;
		$model->value = $value;

		if ($model->save()) {
			$this->metadata->set($name, $model);
			return true;
		}

		return false;
	}

	// -----------------

	private function loadMetadata(): void
	{
		$this->meta_entities = $this->meta_model::where([
			$this->meta_foreign_key => $this->{$this->config->primary_key}
		])->get()->keyBy('name');
	}

}