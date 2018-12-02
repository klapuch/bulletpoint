<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Bulletpoints;

use Bulletpoint\Domain;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;
use Klapuch\Uri;

final class Get implements Application\View {
	/** @var \Klapuch\Uri\Uri */
	private $url;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Uri\Uri $url, Storage\Connection $connection) {
		$this->url = $url;
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$bulletpoints = new Domain\ThemeBulletpoints(
			$parameters['theme_id'],
			$this->connection
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
