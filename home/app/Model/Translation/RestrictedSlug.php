<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class RestrictedSlug implements Slug {
	private $slug;

	public function __construct(Slug $slug) {
		$this->slug = $slug;
	}

	public function origin(): int {
		return $this->slug->origin();
	}

	public function rename(string $newSlug): Slug {
		if(preg_match('~[^a-z0-9_-]+~', $newSlug)) {
			throw new Exception\FormatException(
				sprintf(
					'"%s" není slug',
					$newSlug
				)
			);
		}
		return $this->slug->rename($newSlug);
	}

	public function __toString() {
		return (string)$this->slug;
	}
}