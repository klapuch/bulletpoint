<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Tokens;

use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Output;

final class Delete implements Application\View {
	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Access\TokenEntrance(new Access\FakeEntrance(new Access\Guest())))->exit();
		return new Application\PlainResponse(new Output\EmptyFormat());
	}
}
