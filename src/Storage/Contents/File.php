<?php
/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Storage\Contents;

use Rovota\Framework\Routing\UrlObject;
use Rovota\Framework\Storage\Contents\Extensions\Image\Image;
use Rovota\Framework\Storage\Contents\Extensions\Standard;
use Rovota\Framework\Storage\Contents\Extensions\Text\Text;
use Rovota\Framework\Storage\Interfaces\FileContent;
use Rovota\Framework\Storage\Traits\FileFunctions;
use Stringable;

class File implements Stringable
{
	use FileFunctions;

	// -----------------

	public FileContent|null $contents {
		get => $this->contents;
	}

	public FileProperties $properties {
		get => $this->properties;
	}

	public UrlObject $url {
		get {
			return UrlObject::from($this->properties->disk->url . '/' . $this->location());
		}
	}

	public bool $modified = false;

	// -----------------

	public function __construct(mixed $contents, array $properties, bool $modified = false)
	{
		$this->properties = new FileProperties();
		$this->properties->assign($properties);

		$this->contents = $this->createContentInstance($contents);
		$this->modified = $modified;
	}

	public function __toString(): string
	{
		return $this->contents;
	}

	// -----------------

	protected function createContentInstance(mixed $contents): FileContent|null
	{
		if ($contents instanceof FileContent || $contents === null) {
			return $contents;
		}

		$extensions = [
			'image' => Image::class,
			'text' => Text::class,
			'standard' => Standard::class,
		];

		foreach ($extensions as $extension) {
			/** @var FileContent $extension */
			if ($extension::accepts($contents, $this->properties)) {
				return new $extension($contents, $this);
			}
		}

		return null;
	}

}