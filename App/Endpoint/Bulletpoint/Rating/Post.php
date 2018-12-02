<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Bulletpoint;

use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;
use Klapuch\Uri;

final class Post implements Application\View {
	/** @var \Klapuch\Uri\Uri */
	private $url;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/**
	 * @var Application\Request
	 */
	private $request;

	/**
	 * @var Domain\User
	 */
	private $user;

	public function __construct(Application\Request $request, Uri\Uri $url, Storage\Connection $connection, Domain\User $user) {
		$this->url = $url;
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		['point' => $point] = (new Internal\DecodedJson($this->request->body()->serialization()));
		(new Domain\BulletpointRating(
			$parameters['bulletpoint_id'],
			$this->user,
			$this->connection
		))->rate($point);
		return new Response\EmptyResponse();
	}
}
