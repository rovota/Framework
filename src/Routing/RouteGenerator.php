<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Routing;

use Rovota\Framework\Facades\Route;
use Rovota\Framework\Http\Throttling\LimitManager;
use Rovota\Framework\Routing\Actions\Action;
use Rovota\Framework\Routing\Actions\Extensions\Create;
use Rovota\Framework\Routing\Actions\Extensions\Destroy;
use Rovota\Framework\Routing\Actions\Extensions\Edit;
use Rovota\Framework\Routing\Actions\Extensions\Index;
use Rovota\Framework\Routing\Actions\Extensions\Show;
use Rovota\Framework\Routing\Actions\Extensions\Store;
use Rovota\Framework\Routing\Actions\Extensions\Update;
use Rovota\Framework\Support\Str;
use Rovota\Framework\Support\Traits\Conditionable;

class RouteGenerator
{
	use Conditionable;

	// -----------------

	protected RouteGeneratorConfig $config;

	// -----------------

	public function __construct(string $controller)
	{
		$this->config = new RouteGeneratorConfig([
			'controller' => trim($controller),
			'actions' => $this->getActionConfiguration(),
		]);
	}

	// -----------------

	public function basic(): static
	{
		foreach ($this->config->actions as $name => $options) {
			if ($options['method'] === 'PATCH' || $options['method'] === 'DELETE') {
				$this->config->set('actions.' . $name . '.method', 'POST');
			}
		}
		return $this;
	}

	// -----------------

	public function only(array|string $actions): static
	{
		$actions = is_array($actions) ? $actions : [$actions];

		foreach ($this->config->actions as $name => $options) {
			if (in_array($name, $actions) === false) {
				$this->config->remove('actions.' . $name);
			}
		}
		return $this;
	}

	public function except(array|string $actions): static
	{
		$actions = is_array($actions) ? $actions : [$actions];

		foreach ($this->config->actions as $name => $options) {
			if (in_array($name, $actions)) {
				$this->config->remove('actions.' . $name);
			}
		}
		return $this;
	}

	public function import(array $actions): static
	{
		$show = $this->config->get('actions.show');
		$this->config->remove('actions.show');

		/** @var Action $action */
		foreach ($actions as $name => $action) {
			if (is_int($name)) {
				$name = is_array($action) ? $action['name'] : $action::name();
			}
			$this->config->set('actions.' . $name, is_array($action) ? $action : $action::export());
		}

		$this->config->set('actions.show', $show);
		return $this;
	}

	// -----------------

	public function throttle(array|string $actions, string|null $limiter = null): static
	{
		$actions = is_array($actions) ? $actions : [$actions => $limiter];

		foreach ($actions as $name => $limiter) {
			if ($this->config->has('actions.' . $name)) {
				if ($limiter === false || (is_string($limiter) && LimitManager::instance()->has($limiter))) {
					$this->config->set('actions.' . $name . '.limiter', $limiter);
				}
			}
		}
		return $this;
	}

	// -----------------

	public function confirm(): void
	{
		foreach ($this->config->actions as $action => $options) {
			[$name, $method, $path, $limiter] = array_values($options);

			$action = Str::camel($action);

			if ($method === 'ALL') {
				$instance = Route::all($path, [$this->config->controller, $action])->name($name);
			} else {
				$instance = Route::match($method, $path, [$this->config->controller, $action])->name($name);
			}

			if ($limiter !== false) {
				$instance->throttle($limiter);
			}
		}
	}

	// -----------------

	protected function getActionConfiguration(): array
	{
		return [
			'index' => Index::export(),
			'create' => Create::export(),
			'store' => Store::export(),
			'edit' => Edit::export(),
			'update' => Update::export(),
			'destroy' => Destroy::export(),
			'show' => Show::export(),
		];
	}

}