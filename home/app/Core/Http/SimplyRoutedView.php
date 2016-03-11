<?php
namespace Bulletpoint\Core\Http;

final class SimplyRoutedView implements RoutedView {
	const VIEW = 1;
	const DEFAULT_VIEW = 'default';
	private $url;

	public function __construct(Address $url) {
		$this->url = $url;
	}

	public function view(): string {
		if($this->isAvailable()) {
			if($this->isDefault())
				throw new \RuntimeException(
					sprintf(
						'View %s is not explicilty allowed',
						self::DEFAULT_VIEW
					)
				);
			return $this->url->pathname()[self::VIEW];
		}
		return self::DEFAULT_VIEW;
	}

	private function isAvailable(): bool {
		return isset($this->url->pathname()[self::VIEW])
		&& $this->url->pathname()[self::VIEW];
	}

	private function isDefault(): bool {
		return strcasecmp($this->url->pathname()[self::VIEW], self::DEFAULT_VIEW) === 0;
	}
}