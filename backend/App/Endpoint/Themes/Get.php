<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Themes;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Dataset;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	private const SORTS = [
		'created_at',
	];

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$themes = new Domain\PublicThemes(
			new Domain\StoredThemes(
				$this->user,
				$this->connection
			)
		);
		return new Response\JsonResponse(
			new Response\PlainResponse(
				(new Misc\JsonPrintedObjects(
					static function (Domain\Theme $theme, Output\Format $format): Output\Format {
						return $theme->print($format);
					},
					...iterator_to_array(
						$themes->all(
							new Constraint\AllowedSort(
								new Dataset\RestSort($parameters['sort']),
								self::SORTS
							)
						)
					)
				)),
				['X-Total-Count' => $themes->count(new Dataset\EmptySelection())]
			)
		);
	}
}
