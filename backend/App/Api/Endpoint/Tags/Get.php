<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Tags;

use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	private Storage\Connection $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(new Output\Json((new Domain\StoredTags($this->connection))->all())),
		);
	}
}
