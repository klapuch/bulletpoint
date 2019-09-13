<?php
declare(strict_types = 1);

namespace Bulletpoint\Configuration;

use Klapuch\Configuration;

/**
 * Configuration for whole application
 */
final class ApplicationConfiguration implements Configuration\Source {
	private const ENV_CONFIGURATION = __DIR__ . '/config.env.ini';
	private const SECRET_CONFIGURATION = __DIR__ . '/secrets.ini';
	private const ROUTES = __DIR__ . '/routes.ini';

	public function read(): array {
		return (new Configuration\CachedSource(
			new Configuration\CombinedSource(
				new Configuration\ValidIni(new \SplFileInfo(self::ENV_CONFIGURATION)),
				new Configuration\ValidIni(new \SplFileInfo(self::SECRET_CONFIGURATION)),
				new Configuration\NamedSource(
					'ROUTES',
					new Configuration\ValidIni(new \SplFileInfo(self::ROUTES)),
				),
			),
			self::key(),
		))->read();
	}

	private static function key(): string {
		return (string) crc32(
			implode(
				',',
				array_map(
					'filemtime',
					[
						self::ENV_CONFIGURATION,
						self::SECRET_CONFIGURATION,
						self::ROUTES,
					],
				),
			),
		);
	}
}
