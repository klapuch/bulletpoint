<?php
namespace Bulletpoint\Core\Http;

final class Url implements Address {
	private $scriptUrl;
	private $realUrl;

	public function __construct(string $scriptUrl, string $realUrl) {
		$this->scriptUrl = $scriptUrl;
		$this->realUrl = $realUrl;
	}

	public function pathname(): array {
		return array_values(
			array_filter(
				$this->difference($this->scriptUrl, $this->realUrl),
				function($part) {
					return strlen($part) && !$this->isGetQuery($part);
				}
			)
		);
	}

	public function basename(): string {
		$parts = explode('/', $this->scriptUrl);
		array_pop($parts); // remove index.php or file where is script executed
		return implode('/', array_values($parts)) . '/';
	}

	//?query=someValue
	private function isGetQuery(string $part): bool {
		return (bool)preg_match('~[\?]{1}[\S\s]*=[\S\s]*~', $part);
	}

	private function difference(string $scriptUrl, string $realUrl): array {
		$scriptParts = explode('/', mb_strtolower($scriptUrl));
		$realParts = explode('/', mb_strtolower($realUrl));
		$iteration = count($scriptParts);
		for($i = 0; $i < $iteration; $i++) {
			if($scriptParts[$i] !== $realParts[$i])
				return array_slice($realParts, $i);
		}
	}
}