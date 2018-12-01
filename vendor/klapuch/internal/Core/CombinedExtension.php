<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class CombinedExtension implements Extension {
	private $extensions;

	public function __construct(Extension ...$extensions) {
		$this->extensions = $extensions;
	}

	public function improve(): void {
		foreach ($this->extensions as $extension)
			$extension->improve();
	}
}