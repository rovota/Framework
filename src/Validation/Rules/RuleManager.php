<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Validation\Rules;

use Rovota\Framework\Validation\Interfaces\RuleInterface;
use Rovota\Framework\Validation\Rules\Advanced\AcceptedRule;
use Rovota\Framework\Validation\Rules\Advanced\DeclinedRule;
use Rovota\Framework\Validation\Rules\Advanced\DifferentRule;
use Rovota\Framework\Validation\Rules\Advanced\EmailRule;
use Rovota\Framework\Validation\Rules\Advanced\EqualRule;
use Rovota\Framework\Validation\Rules\Advanced\ExistsRule;
use Rovota\Framework\Validation\Rules\Advanced\HashRule;
use Rovota\Framework\Validation\Rules\Advanced\HibpRule;
use Rovota\Framework\Validation\Rules\Advanced\NotRegexRule;
use Rovota\Framework\Validation\Rules\Advanced\RegexRule;
use Rovota\Framework\Validation\Rules\Advanced\RequiredIfAccepted;
use Rovota\Framework\Validation\Rules\Advanced\RequiredIfDeclined;
use Rovota\Framework\Validation\Rules\Advanced\UniqueRule;
use Rovota\Framework\Validation\Rules\DateTime\AfterOrEqualRule;
use Rovota\Framework\Validation\Rules\DateTime\AfterRule;
use Rovota\Framework\Validation\Rules\DateTime\BeforeOrEqualRule;
use Rovota\Framework\Validation\Rules\DateTime\BeforeRule;
use Rovota\Framework\Validation\Rules\DateTime\BetweenDatesRule;
use Rovota\Framework\Validation\Rules\DateTime\DateEqualsRule;
use Rovota\Framework\Validation\Rules\DateTime\DateFormatRule;
use Rovota\Framework\Validation\Rules\DateTime\OutsideDatesRule;
use Rovota\Framework\Validation\Rules\DateTime\TimezoneRule;
use Rovota\Framework\Validation\Rules\Standard\BetweenRule;
use Rovota\Framework\Validation\Rules\Standard\CaseRule;
use Rovota\Framework\Validation\Rules\Standard\ContainsAnyRule;
use Rovota\Framework\Validation\Rules\Standard\ContainsNoneRule;
use Rovota\Framework\Validation\Rules\Standard\ContainsRule;
use Rovota\Framework\Validation\Rules\Standard\EndsWithRule;
use Rovota\Framework\Validation\Rules\Standard\GreaterThanOrEqualRule;
use Rovota\Framework\Validation\Rules\Standard\GreaterThanRule;
use Rovota\Framework\Validation\Rules\Standard\InRule;
use Rovota\Framework\Validation\Rules\Standard\LessThanOrEqualRule;
use Rovota\Framework\Validation\Rules\Standard\LessThanRule;
use Rovota\Framework\Validation\Rules\Standard\MaxRule;
use Rovota\Framework\Validation\Rules\Standard\MinRule;
use Rovota\Framework\Validation\Rules\Standard\NotInRule;
use Rovota\Framework\Validation\Rules\Standard\RangeRule;
use Rovota\Framework\Validation\Rules\Standard\SizeRule;
use Rovota\Framework\Validation\Rules\Standard\StartsWithRule;
use Rovota\Framework\Validation\Rules\Storage\ExtensionsRule;
use Rovota\Framework\Validation\Rules\Storage\MimesRule;
use Rovota\Framework\Validation\Rules\Storage\MimeTypesRule;
use Rovota\Framework\Validation\Rules\Typing\ArrayRule;
use Rovota\Framework\Validation\Rules\Typing\BooleanRule;
use Rovota\Framework\Validation\Rules\Typing\EnumRule;
use Rovota\Framework\Validation\Rules\Typing\FileRule;
use Rovota\Framework\Validation\Rules\Typing\FloatRule;
use Rovota\Framework\Validation\Rules\Typing\IntegerRule;
use Rovota\Framework\Validation\Rules\Typing\ListRule;
use Rovota\Framework\Validation\Rules\Typing\MomentRule;
use Rovota\Framework\Validation\Rules\Typing\NumericRule;
use Rovota\Framework\Validation\Rules\Typing\StringRule;

final class RuleManager
{

	protected static array $rules = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::registerAdvancedRules();
		self::registerDateTimeRules();
		self::registerStandardRules();
		self::registerStorageRules();
		self::registerTypingRules();
	}

	// -----------------

	public static function register(string $name, string $class): void
	{
		self::$rules[$name] = $class;
	}

	public static function get(string $name): RuleInterface|null
	{
		return isset(self::$rules[$name]) ? new self::$rules[$name]($name) : null;
	}

	// -----------------

	protected static function registerAdvancedRules(): void
	{
		self::register('accepted', AcceptedRule::class);
		self::register('declined', DeclinedRule::class);
		self::register('different', DifferentRule::class);
		self::register('email', EmailRule::class);
		self::register('equal', EqualRule::class);
		self::register('exists', ExistsRule::class);
		self::register('hash', HashRule::class);
		self::register('hibp', HibpRule::class);
		self::register('not_regex', NotRegexRule::class);
		self::register('regex', RegexRule::class);
		self::register('required_if_accepted', RequiredIfAccepted::class);
		self::register('required_if_declined', RequiredIfDeclined::class);
		self::register('unique', UniqueRule::class);
	}

	protected static function registerDateTimeRules(): void
	{
		self::register('after', AfterRule::class);
		self::register('after_or_equal', AfterOrEqualRule::class);
		self::register('before', BeforeRule::class);
		self::register('before_or_equal', BeforeOrEqualRule::class);
		self::register('between_dates', BetweenDatesRule::class);
		self::register('outside_dates', OutsideDatesRule::class);
		self::register('date_equals', DateEqualsRule::class);
		self::register('date_format', DateFormatRule::class);
		self::register('timezone', TimezoneRule::class);
	}
	
	protected static function registerStandardRules(): void
	{
		self::register('max', MaxRule::class);
		self::register('min', MinRule::class);
		self::register('size', SizeRule::class);
		self::register('between', BetweenRule::class);
		self::register('range', RangeRule::class);
		self::register('gt', GreaterThanRule::class);
		self::register('gte', GreaterThanOrEqualRule::class);
		self::register('lt', LessThanRule::class);
		self::register('lte', LessThanOrEqualRule::class);

		self::register('case', CaseRule::class);
		self::register('starts_with', StartsWithRule::class);
		self::register('ends_with', EndsWithRule::class);
		self::register('contains', ContainsRule::class);
		self::register('contains_any', ContainsAnyRule::class);
		self::register('contains_none', ContainsNoneRule::class);
		self::register('in', InRule::class);
		self::register('not_in', NotInRule::class);
	}

	protected static function registerStorageRules(): void
	{
		self::register('extensions', ExtensionsRule::class);
		self::register('mimes', MimesRule::class);
		self::register('mime_types', MimeTypesRule::class);
	}

	protected static function registerTypingRules(): void
	{
		self::register('array', ArrayRule::class);
		self::register('bool', BooleanRule::class);
		self::register('enum', EnumRule::class);
		self::register('file', FileRule::class);
		self::register('float', FloatRule::class);
		self::register('int', IntegerRule::class);
		self::register('list', ListRule::class);
		self::register('moment', MomentRule::class);
		self::register('numeric', NumericRule::class);
		self::register('string', StringRule::class);
	}

}