<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Themes;

use Bulletpoint\Constraint;
use Bulletpoint\Dataset\RestFilter;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Dataset;
use Klapuch\Output;
use Klapuch\Storage;
use Klapuch\UI;
use Klapuch\Uri\Uri;

final class Get implements Application\View {
	private const ALLOWED_FILTERS = [
		'is_starred',
	];
	private const SORTS = [
		'created_at',
	];

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Uri\Uri */
	private $uri;

	public function __construct(Storage\Connection $connection, Uri $uri) {
		$this->connection = $connection;
		$this->uri = $uri;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$tags = array_map('intval', array_filter(explode(',', (string) ($parameters['tag_id'] ?? '')), 'strlen'));
		$q = $parameters['q'] ?? null;
		unset($parameters['tag_id'], $parameters['q']);
		if ($tags !== [] && $q !== null) {
			$themes = new Domain\SearchTaggedThemes(
				new Domain\FakeThemes(),
				(string) $q,
				$tags,
				$this->connection,
			);
		} elseif ($tags !== []) {
			$themes = new Domain\TaggedThemes(
				new Domain\FakeThemes(),
				$tags,
				$this->connection,
			);
		} elseif ($q !== null) {
			$themes = new Domain\SearchedThemes(
				new Domain\FakeThemes(),
				(string) $q,
				$this->connection,
			);
		} else {
			$themes = new Domain\StoredThemes(
				new Access\FakeUser(),
				$this->connection,
			);
		}
		$themes = new Domain\PublicThemes($themes);
		$count = $themes->count(new RestFilter($parameters, self::ALLOWED_FILTERS));
		return new Response\PaginatedResponse(
			new Response\JsonResponse(
				new Application\PlainResponse(
					(new Output\JsonPrintedObjects(
						static function (Domain\Theme $theme, Output\Format $format): Output\Format {
							return $theme->print($format);
						},
						...iterator_to_array(
							$themes->all(
								new Dataset\CombinedSelection(
									new RestFilter($parameters, self::ALLOWED_FILTERS),
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
					['X-Total-Count' => $count],
				),
			),
			$parameters['page'],
			new UI\AttainablePagination($parameters['page'], $parameters['per_page'], $count),
			$this->uri,
		);
	}
}
