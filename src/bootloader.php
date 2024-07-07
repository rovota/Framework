<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

use Rovota\Framework\Kernel\Application;
use Rovota\Framework\Kernel\ExceptionHandler;

// -----------------

// Start buffering output
ob_start();

// -----------------

// Set a baseline for date/time usage
date_default_timezone_set('UTC');

// -----------------

ExceptionHandler::initialize();

set_exception_handler(function(Throwable $throwable) {
	ExceptionHandler::handleThrowable($throwable);
});

set_error_handler(function(int $number, string $message, string $file, int $line) {
	ExceptionHandler::handleError($number, $message, $file, $line);
});

register_shutdown_function(fn () => Application::shutdown());

// -----------------

// Bind global helper functions
require 'functions.php';

// -----------------

Application::start();