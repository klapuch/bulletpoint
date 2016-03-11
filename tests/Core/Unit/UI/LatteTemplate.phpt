<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Core\UI;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class LatteTemplate extends TestCase\Filesystem {
	private $template;
	private $latte;
	const FOLDER = __DIR__ . '/temp/';

	protected function setUp() {
		parent::setUp();
		$this->latte = $this->mockery('Latte\Engine');
		$this->template = new UI\LatteTemplate(
			new Fake\Path(self::FOLDER, '%s', '.phtml'),
			$this->latte,
			new Fake\RoutedView('view')
		);
	}

	public function testCorrectPath() {
		$this->latte->shouldReceive('render')->once();
		$this->latte->shouldReceive('addFilter')->once();
		$this->preparedFilesystem();
		$this->template->render('layout');
		Assert::true(true);
	}

	public function testFilter() {
		$this->latte->shouldReceive('addFilter')->once();
		$this->template->addFilter('newFilter', function() {return 6;});
		Assert::true(true);
	}

	public function testSet() {
		$this->template->x = 10;
		$this->template->x = 666;
		Assert::same(666, $this->template->x);
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . 'layout.phtml', 'data');
	}
}


(new LatteTemplate())->run();
