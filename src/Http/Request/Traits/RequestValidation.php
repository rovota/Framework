<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Http\Request\Traits;

use Rovota\Framework\Caching\CacheManager;
use Rovota\Framework\Caching\Enums\Driver;
use Rovota\Framework\Caching\Interfaces\CacheInterface;
use Rovota\Framework\Http\Request\RequestData;
use Rovota\Framework\Support\MessageBag;
use Rovota\Framework\Validation\Validator;

trait RequestValidation
{

	public readonly MessageBag $errors;

	public readonly RequestData $safe;

	// -----------------

	public function validate(array $rules = [], array $messages = []): bool
	{
		$validator = Validator::create($this->all(), $rules, $messages);
		$validator->validate();

		$this->errors->import($validator->errors);
		$this->safe->import($validator->safe);

		return $validator->errors->isEmpty();
	}

	// -----------------

	protected function loadErrorDataFromSession(): void
	{
		$store = CacheManager::instance()->getWithDriver(Driver::Session);

		if ($store instanceof CacheInterface) {
			$this->errors->import($store->pull('error_messages'));
		}
	}

}