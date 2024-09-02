<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Database\Model;

class User extends Model
{

	protected array $casts = [
		'last_active' => 'moment',
	];

	protected array $fillable = [
		'username'
	];

}