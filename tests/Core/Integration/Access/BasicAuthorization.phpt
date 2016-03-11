<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Integration;

use Tester;
use Tester\Assert;
use Bulletpoint\Core\Access;
use Bulletpoint\Core\Control;
use Bulletpoint\Fake;
use Bulletpoint\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class BasicAuthorization extends TestCase\Filesystem {
	private $authorization;
	const FOLDER = __DIR__ . '/temp/';
	const INI_FILE = '.config.ini';

	public function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->authorization = new Access\BasicAuthorization(
			new Access\RoleBasedAcl(
				new Control\IniConfiguration(self::FOLDER . self::INI_FILE),
				'member'
			),
			new Access\WildcardComparison
		);
	}

	protected function forbiddenAddresses() {
		return [
			[['ban', 'whatever']],
			[['bany', 'whatever']],
			[['banx', 'whatever']],
			[['ban', '']], // ban/
			[['administrace']], // administrace
			[['bulletpoint', 'upravitWhatever']],
			[['bulletpoint', 'upravit', '123']],
		];
	}

	/**
	* @dataProvider forbiddenAddresses
	*/
	public function testAllowedAddresses($address) {
		Assert::false($this->authorization->hasAccess(
			new Fake\Address($address))
		);
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . self::INI_FILE, INI);
	}
}

const INI = <<<INI
[ACL]
member = "
	administrace,
	ban?/*,
	bulletpoint/upravit*,
"
INI;

(new BasicAuthorization())->run();