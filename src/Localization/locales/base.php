<?php
/** @noinspection ALL */

/**
 * Inspired by briannesbitt/Carbon
 */

return [

	'about' => [
		'label' => [
			'default' => 'Unknown',
			'native' => 'Unknown',
			'local' => __('Unknown'),
		],

		'direction' => 'ltr',
		'system' => 'metric',
	],

	// 'date' => [
	// 	'formats' => [
	// 		'' => '',
	// 	],
	// 	'first_day_of_week' => 1
	// ],

	'units' => [
		'numbers' => [
			'short' => ['', 'K', 'M', 'B', 'T', 'Q'],
			'long' => ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion']
		],
		'storage' => [
			'short' => ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
			'long' => ['bytes', 'kilobytes', 'megabytes', 'gigabytes', 'terabytes', 'petabytes', 'exabytes', 'zettabytes', 'yottabytes']
		],
	],

];