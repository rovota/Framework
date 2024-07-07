<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */



// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------

// -----------------
// Internal

if (!function_exists('source')) {
	function source(string $path = ''): string
	{
		return local($path, dirname(__FILE__));
	}
}

if (!function_exists('local')) {
	function local(string $path = '', string|null $base = null): string
	{
		$base = $base ?? (defined('BASE_PATH') ? BASE_PATH : dirname(__FILE__));
		return strlen($path) > 0 ? $base.'/'.ltrim($path, '/') : $base;
	}
}