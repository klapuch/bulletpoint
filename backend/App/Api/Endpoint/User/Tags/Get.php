<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\User\Tags;

use Bulletpoint\Constraint;
use Bulletpoint\Dataset\RestFilter;
use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\User;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Dataset;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	private const ALLOWED_FILTERS = ['tag_id'];
	private const SORTS = [
		'reputation',
		'rank',
	];
	private const PARAMETERS = ['id'];

	private Storage\Connection $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function response(array $parameters): Application\Response {
		if (isset($parameters['tag_id'])) {
			$parameters['tag_id'] = array_map('intval', array_filter((array) ($parameters['tag_id'] ?? []), 'strlen'));
		}
		return new Response\JsonResponse(
			new Application\PlainResponse(
				(new Output\JsonPrintedObjects(
					static fn (User\Tag $tag, Output\Format $format): Output\Format => $tag->print($format),
					...iterator_to_array(
						(new User\StoredTags(
							$this->connection,
							new Access\RegisteredUser($parameters['id'], $this->connection),
						))->all(
							new Dataset\CombinedSelection(
								new RestFilter($parameters, self::ALLOWED_FILTERS, self::PARAMETERS),
								new Constraint\AllowedSort(
									new Dataset\RestSort($parameters['sort']),
									self::SORTS,
								),
								new Dataset\RestPaging(
									$parameters['page'],
									$parameters['per_page'],
								),
							),
						),
					),
				)),
			),
		);
	}
}
