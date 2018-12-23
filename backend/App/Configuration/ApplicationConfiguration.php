<?php
declare(strict_types = 1);

namespace Bulletpoint\Configuration;

use Klapuch\Configuration;

/**
 * Configuration for whole application
 */
final class ApplicationConfiguration implements Configuration\Source {
	private const CONFIGURATION = __DIR__ . '/config.ini',
		SECRET_CONFIGURATION = __DIR__ . '/secrets.ini',
		ROUTES = __DIR__ . '/routes.ini';

	public function read(): array {
		return (new Configuration\CachedSource(
			new Configuration\CombinedSource(
				new Configuration\ValidIni(new \SplFileInfo(self::CONFIGURATION)),
				new Configuration\ValidIni(new \SplFileInfo(self::SECRET_CONFIGURATION)),
				new Configuration\NamedSource(
					'ROUTES',
					new Configuration\ValidIni(new \SplFileInfo(self::ROUTES))
				)
			),
			$this->key()
		))->read();
	}

	private function key(): string {
		return (string) crc32(
			array_reduce(
				[
					self::CONFIGURATION,
					self::SECRET_CONFIGURATION,
					self::ROUTES,
				],
				static function(string $key, string $location): string {
					return $key . filemtime($location);
				},
				''
			)
		);
	}
}
