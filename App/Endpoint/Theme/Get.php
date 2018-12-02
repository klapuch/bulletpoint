<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Theme;

use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;
use Klapuch\Uri;

final class Get implements Application\View {
	/** @var \Klapuch\Uri\Uri */
	private $url;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Uri\Uri $url, Storage\Connection $connection) {
		$this->url = $url;
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Response\PlainResponse(
				(new Domain\ExistingTheme(
					new Domain\StoredTheme($parameters['id'], $this->connection),
					$parameters['id'],
					$this->connection
				))->print(new Output\Json())
			)
		);
	}
}
