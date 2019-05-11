<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class TokenEntranceTest extends Tester\TestCase {
	public function testRetrievedUserSessionIdOnEntering(): void {
		Assert::match(
			'~^[\w\d,-]{60}$~',
			(new Access\TokenEntrance(
				new Access\FakeEntrance(new Access\FakeUser('1', [])),
			))->enter([])->id(),
		);
	}

	public function testEnteringWithSetSession(): void {
		(new Access\TokenEntrance(
			new Access\FakeEntrance(new Access\FakeUser('1', [])),
		))->enter([]);
		Assert::same(1, $_SESSION['id']);
	}

	public function testNewIdOnEachEntering(): void {
		$entrance = new Access\TokenEntrance(
			new Access\FakeEntrance(new Access\FakeUser('1', [])),
		);
		Assert::notSame($entrance->enter([])->id(), $entrance->enter([])->id());
	}

	public function testExitingWithDelegation(): void {
		$user = new Access\FakeUser('1');
		Assert::same(
			$user,
			(new Access\TokenEntrance(
				new Access\FakeEntrance($user),
			))->exit(),
		);
	}
}

(new TokenEntranceTest())->run();
