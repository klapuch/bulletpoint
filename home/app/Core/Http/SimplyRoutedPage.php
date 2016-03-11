<?php
namespace Bulletpoint\Core\Http;

final class SimplyRoutedPage implements RoutedPage {
	const PAGE = 0;
	const DEFAULT_PAGE = 'default';
	private $url;

	public function __construct(Address $url) {
		$this->url = $url;
	}

	public function page(): string {
		if($this->isAvailable()) {
			if($this->isDefault())
				throw new \RuntimeException(
					sprintf(
						'Page %s is not explicilty allowed',
						self::DEFAULT_PAGE
					)
				);
			return $this->url->pathname()[self::PAGE];
		}
		return self::DEFAULT_PAGE;
	}

	private function isAvailable(): bool {
		return isset($this->url->pathname()[self::PAGE])
		&& $this->url->pathname()[self::PAGE];
	}

	private function isDefault(): bool {
		return strcasecmp($this->url->pathname()[self::PAGE], self::DEFAULT_PAGE) === 0;
	}
}