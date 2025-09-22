<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Structures\Extensions;

use Rovota\Framework\Identity\Models\Guard;
use Rovota\Framework\Structures\Bucket;

class GuardBag extends Bucket
{

	public function retrieve(string $type): Guard|null
	{
		return $this->get($type);
	}

}