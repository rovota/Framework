<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Support\Enums;

enum PHPErrorLevel: int
{

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
			PHPErrorLevel::Error => 'Error',
			PHPErrorLevel::Warning => 'Warning',
			PHPErrorLevel::Parse => 'Parse',
			PHPErrorLevel::Notice => 'Notice',
			PHPErrorLevel::CoreError => 'Core Error',
			PHPErrorLevel::CoreWarning => 'Core Warning',
			PHPErrorLevel::CompileError => 'Compile Error',
			PHPErrorLevel::CompileWarning => 'Compile Warning',
			PHPErrorLevel::UserError => 'User Error',
			PHPErrorLevel::UserWarning => 'User Warning',
			PHPErrorLevel::UserNotice => 'User Notice',
			PHPErrorLevel::Strict => 'Strict',
			PHPErrorLevel::RecoverableError => 'Recoverable Error',
			PHPErrorLevel::Deprecated => 'Deprecated',
			PHPErrorLevel::UserDeprecated => 'User Deprecated',
		};
	}

}