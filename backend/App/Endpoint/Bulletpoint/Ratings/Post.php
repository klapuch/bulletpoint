<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Bulletpoint\Ratings;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Application\Request $request, Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		['point' => $point] = (new Constraint\StructuredJson(
			new \SplFileInfo(self::SCHEMA)
		))->apply((new Internal\DecodedJson($this->request->body()->serialization()))->values());
		(new Domain\BulletpointRating(
			$parameters['bulletpoint_id'],
			$this->user,
			$this->connection
		))->rate($point);
		return new Response\EmptyResponse();
	}
}
