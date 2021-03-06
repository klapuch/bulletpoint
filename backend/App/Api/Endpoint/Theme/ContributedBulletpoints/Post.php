<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Theme\ContributedBulletpoints;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Klapuch\Application;
use Klapuch\Storage;
use Klapuch\Validation;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';
	private Storage\Connection $connection;
	private Application\Request $request;
	private Access\User $user;

	public function __construct(Application\Request $request, Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Contribution\ThemeBulletpoints(
			$parameters['theme_id'],
			$this->connection,
			$this->user,
		))->add(
			(new Validation\ChainedRule(
				new Constraint\StructuredJson(new \SplFileInfo(self::SCHEMA)),
				new Constraint\Bulletpoint\Rule($this->connection),
			))->apply(Json::decode($this->request->body()->serialization())),
		);
		return new Application\EmptyResponse();
	}
}
