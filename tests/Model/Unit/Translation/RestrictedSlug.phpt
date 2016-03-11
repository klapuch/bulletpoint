<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Translation;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class RestrictedSlug extends TestCase\Database {
	protected function validSlugs() {
		return [
			['someslug'],
			['some-slug'],
			['some_slug'],
			['2morrow'],
			['this-is_some_slug5'],
			['5'],
			['00'],
		];
	}

	protected function invalidSlugs() {
		return [
			['this is not slug'],
			['ThisIsNotSlug'],
			['This-is-not-slug'],
			['what%are%you%saying'],
		];
	}

	/**
	* @dataProvider invalidSlugs
	*/
	public function testRenamingInvalidSlugs($slug) {
		Assert::exception(function() use($slug) {
			(new Translation\RestrictedSlug(new Fake\Slug))
			->rename($slug);
		}, 'Bulletpoint\Exception\FormatException', "\"$slug\" není slug");
	}

	/**
	* @dataProvider validSlugs
	*/
	public function testRenamingValidSlugs($slug) {
		(new Translation\RestrictedSlug(new Fake\Slug))
		->rename($slug);
		Assert::true(true);
	}
}


(new RestrictedSlug())->run();
