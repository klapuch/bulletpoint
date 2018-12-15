<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Themes;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Http;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;
use Klapuch\Uri\RelativeUrl;
use Klapuch\Uri\Uri;

final class Post implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Uri\Uri */
	private $url;

	public function __construct(Application\Request $request, Storage\Connection $connection, Access\User $user, Uri $url) {
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
		$this->url = $url;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		return new Response\CreatedResponse(
			new Response\EmptyResponse(),
			new Http\CreatedResourceUrl(
				new RelativeUrl($this->url, 'themes/{id}'),
				[
					'id' => (new Domain\StoredThemes(
						$this->user,
						$this->connection
					))->create((new Internal\DecodedJson($this->request->body()->serialization()))->values()),
				]
			)
		);
	}
}
