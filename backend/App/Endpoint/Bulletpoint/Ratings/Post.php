<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Bulletpoint\Ratings;

use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;

final class Post implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Bulletpoint\Domain\User */
	private $user;

	public function __construct(Application\Request $request, Storage\Connection $connection, Domain\User $user) {
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		['point' => $point] = (new Internal\DecodedJson($this->request->body()->serialization()))->values();
		(new Domain\BulletpointRating(
			$parameters['bulletpoint_id'],
			$this->user,
			$this->connection
		))->rate($point);
		return new Response\EmptyResponse();
	}
}
