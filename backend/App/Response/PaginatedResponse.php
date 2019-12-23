<?php
declare(strict_types = 1);

namespace Bulletpoint\Response;

use Bulletpoint\Http;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\UI;
use Klapuch\Uri\Uri;

/**
 * Partial response suited for pagination
 * Returning 206 status code for not last page
 */
final class PaginatedResponse implements Application\Response {
	private Application\Response $origin;
	private int $page;
	private UI\Pagination $pagination;
	private Uri $uri;

	public function __construct(
		Application\Response $origin,
		int $page,
		UI\Pagination $pagination,
		Uri $uri
	) {
		$this->origin = $origin;
		$this->page = $page;
		$this->pagination = $pagination;
		$this->uri = $uri;
	}

	public function body(): Output\Format {
		return $this->origin->body();
	}

	public function headers(): array {
		return [
			'Link' => $this->pagination->print(new Http\HeaderLink($this->uri))->serialization(),
		] + $this->origin->headers();
	}

	public function status(): int {
		return $this->page >= current(array_slice($this->pagination->range(), -1))
			? $this->origin->status()
			: HTTP_PARTIAL_CONTENT;
	}
}
