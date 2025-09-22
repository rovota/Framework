<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Interfaces;

use Rovota\Framework\Storage\Contents\FileProperties;

interface FileContent
{

	public static function accepts(mixed $data, FileProperties $properties): bool;

}