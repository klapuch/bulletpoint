<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Tags;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Klapuch\Application;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Application\Request */
	private $request;

	public function __construct(Storage\Connection $connection, Application\Request $request) {
		$this->connection = $connection;
		$this->request = $request;
	}

	public function response(array $parameters): Application\Response {
		$tag = (new Constraint\StructuredJson(
			new \SplFileInfo(self::SCHEMA),
		))->apply(Json::decode($this->request->body()->serialization()));
		(new Domain\UniqueTags(
			new Domain\StoredTags($this->connection),
			$this->connection,
		))->add($tag['name']);
		return new Application\EmptyResponse();
	}
}
