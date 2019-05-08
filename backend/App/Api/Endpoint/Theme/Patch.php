<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Theme;

use Bulletpoint\Api\Http;
use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Patch implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/patch.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

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
		$payload = (new Constraint\StructuredJson(
			new \SplFileInfo(self::SCHEMA),
		))->apply(Json::decode($this->request->body()->serialization()));
		$theme = new Http\ErrorTheme(
			HTTP_NOT_FOUND,
			new Domain\ExistingTheme(
				new Domain\StoredTheme($parameters['id'], $this->connection, $this->user),
				$parameters['id'],
				$this->connection,
			),
		);
		if (isset($payload['is_starred'])) {
			if ($payload['is_starred'] === true)
				$theme->star();
			else
				$theme->unstar();
		}
		return new Application\EmptyResponse();
	}
}
