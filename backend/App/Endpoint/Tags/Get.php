<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Tags;

use Bulletpoint\Response;
use Bulletpoint\Schema;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(new Output\Json((new Schema\TableEnum('tags', $this->connection))->values())),
		);
	}
}
