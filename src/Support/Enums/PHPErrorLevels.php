<?php

/**
 * @copyright   Copyright (c), Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Enums;

use Rovota\Framework\Support\Traits\EnumHelpers;

enum PHPErrorLevels: int
{
	use EnumHelpers;

	case Error = 1;
	case Warning = 2;
	case Parse = 4;
	case Notice = 8;
	case CoreError = 16;
	case CoreWarning = 32;
	case CompileError = 64;
	case CompileWarning = 128;
	case UserError = 256;
	case UserWarning = 512;
	case UserNotice = 1024;
	case Strict = 2048;
	case RecoverableError = 4096;
	case Deprecated = 8192;
	case UserDeprecated = 16384;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			PHPErrorLevels::Error => 'Error',
			PHPErrorLevels::Warning => 'Warning',
			PHPErrorLevels::Parse => 'Parse',
			PHPErrorLevels::Notice => 'Notice',
			PHPErrorLevels::CoreError => 'Core Error',
			PHPErrorLevels::CoreWarning => 'Core Warning',
			PHPErrorLevels::CompileError => 'Compile Error',
			PHPErrorLevels::CompileWarning => 'Compile Warning',
			PHPErrorLevels::UserError => 'User Error',
			PHPErrorLevels::UserWarning => 'User Warning',
			PHPErrorLevels::UserNotice => 'User Notice',
			PHPErrorLevels::Strict => 'Strict',
			PHPErrorLevels::RecoverableError => 'Recoverable Error',
			PHPErrorLevels::Deprecated => 'Deprecated',
			PHPErrorLevels::UserDeprecated => 'User Deprecated',
		};
	}

}