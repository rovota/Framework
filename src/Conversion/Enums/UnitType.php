<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Conversion\Enums;

use Rovota\Framework\Conversion\Units\Frequency;
use Rovota\Framework\Conversion\Units\FuelEconomy;
use Rovota\Framework\Conversion\Units\Length;
use Rovota\Framework\Conversion\Units\Mass;
use Rovota\Framework\Conversion\Units\Pressure;
use Rovota\Framework\Conversion\Units\Speed;
use Rovota\Framework\Conversion\Units\Temperature;
use Rovota\Framework\Conversion\Units\Time;
use Rovota\Framework\Conversion\Units\Volume;

enum UnitType: int
{

//	case Area = 1;
//	case DataTransferRate = 2;
//	case DigitalStorage = 3;
//	case Energy = 4;
	case Frequency = 5;
	case FuelEconomy = 6;
	case Length = 7;
	case Mass = 8;
	case Pressure = 9;
	case Speed = 10;
	case Temperature = 11;
	case Time = 12;
	case Volume = 13;

	// -----------------

	public function class(): string
	{
		return match ($this) {
//			UnitType::Area => '',
//			UnitType::DataTransferRate => '',
//			UnitType::DigitalStorage => '',
//			UnitType::Energy => '',
			UnitType::Frequency => Frequency::class,
			UnitType::FuelEconomy => FuelEconomy::class,
			UnitType::Length => Length::class,
			UnitType::Mass => Mass::class,
			UnitType::Pressure => Pressure::class,
			UnitType::Speed => Speed::class,
			UnitType::Temperature => Temperature::class,
			UnitType::Time => Time::class,
			UnitType::Volume => Volume::class,
		};
	}

}