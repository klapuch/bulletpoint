<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Themes;

use Bulletpoint\Constraint;
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
		if (isset($parameters['tag_id'])) {
			$themes = new Domain\PublicThemes(
				new Domain\TaggedThemes(
					new Domain\FakeThemes(),
					array_map('intval', array_filter(explode(',', (string) $parameters['tag_id']), 'strlen')),
					$this->connection,
				),
			);
		} elseif (isset($parameters['q'])) {
			$themes = new Domain\PublicThemes(
				new Domain\SearchedThemes(
					new Domain\FakeThemes(),
					(string) $parameters['q'],
					$this->connection,
				),
			);
		} else {
			$themes = new Domain\PublicThemes(
				new Domain\StoredThemes(
					new Access\FakeUser(),
					$this->connection,
				),
			);
		}
		$count = $themes->count(new Dataset\EmptySelection());
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
