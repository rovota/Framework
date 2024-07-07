<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

use Rovota\Framework\Kernel\Application;
use Rovota\Framework\Kernel\ExceptionHandler;

// -----------------

// Open a buffer for all output
ob_start();

// -----------------

// Set a baseline for date/time usage
date_default_timezone_set('UTC');

// -----------------

ExceptionHandler::initialize();

// -----------------

// Bind global helper functions
require 'functions.php';

// -----------------

Application::start();


echo Application::$version->jsonSerialize();