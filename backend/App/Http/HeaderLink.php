<?php
declare(strict_types = 1);

namespace Bulletpoint\Http;

use Klapuch\Output;
use Klapuch\Uri\Uri;

/**
 * Format generating links for headers in format
 * <https://example.com>; rel="homepage", ...
 */
final class HeaderLink implements Output\Format {
	private Uri $uri;

	/** @var mixed[] */
	private array $moves;

	public function __construct(Uri $uri, array $moves = []) {
		$this->uri = $uri;
		$this->moves = $moves;
	}

	/**
	 * @param mixed $tag
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $content = null): Output\Format {
		return new self($this->uri, [$tag => $content] + $this->moves);
	}

	public function serialization(): string {
		return implode(
			', ',
			array_map(
				fn(string $direction, int $page): string => sprintf(
					'<%s/%s?%s>; rel="%s"',
					rtrim($this->uri->reference(), '/'),
					ltrim($this->uri->path(), '/'),
					http_build_query(
						['page' => $page] + $this->uri->query(),
						'',
						'&',
						PHP_QUERY_RFC3986,
					),
					$direction,
				),
				array_keys($this->moves),
				$this->moves,
			),
		);
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Output\Format {
		return new self(
			$this->uri,
			[$tag => call_user_func($adjustment, $this->moves[$tag])] + $this->moves,
		);
	}
}
