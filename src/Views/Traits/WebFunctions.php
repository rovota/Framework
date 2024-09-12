<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Views\Traits;

use Rovota\Framework\Support\Str;

trait WebFunctions
{

	public function withTitle(string $title): static
	{
		$title = Str::translate(trim($title));

		$this->with('meta.title', $title);
		$this->withMeta('og:title', ['name' => 'og:title', 'content' => $title]);

		return $this;
	}

	public function withDescription(string $description): static
	{
		$description = Str::translate(trim($description));

		$this->with('meta.description', $description);
		$this->withMeta('description', ['name' => 'description', 'content' => $description]);
		$this->withMeta('og:description', ['name' => 'og:description', 'content' => $description]);

		return $this;
	}

	public function withKeywords(array $keywords): static
	{
		$keywords = implode(',', $keywords);

		$this->with('meta.keywords', $keywords);
		$this->withMeta('keywords', ['name' => 'keywords', 'content' => $keywords]);

		return $this;
	}

	// -----------------

	public function withAuthor(string $author): static
	{
		$this->with('meta.author', $author);
		$this->withMeta('author', ['name' => 'author', 'content' => $author], true);

		return $this;
	}

	// -----------------

//	TODO: Add withImage method based on the method below.
//
//	public function setImage(FileInterface|string $location): static
//	{
//		$public_url = match (true) {
//			$location instanceof FileInterface => $location->publicUrl(),
//			default => $location,
//		};
//
//		PartialManager::addOrUpdateVariable('*', 'page', [
//			'image' => $public_url,
//		]);
//
//		$this->meta('og:image', ['content' => $public_url]);
//		$this->meta('og:image:secure_url', ['content' => $public_url]);
//		$this->meta('twitter:image', ['content' => $public_url]);
//
//		return $this;
//	}

}