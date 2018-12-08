<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Theme\Bulletpoints;

use Bulletpoint\Domain;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$bulletpoints = new Domain\ThemeBulletpoints(
			$parameters['theme_id'],
			$this->connection,
			new Domain\FakeUser()
		);
		return new Response\JsonResponse(
			new Response\PlainResponse(
				(new Misc\JsonPrintedObjects(
					static function (Domain\Bulletpoint $bulletpoint, Output\Format $format): Output\Format {
						return $bulletpoint->print($format);
					},
					...iterator_to_array($bulletpoints->all())
				)),
				['X-Total-Count' => $bulletpoints->count()]
			)
		);
	}
}
