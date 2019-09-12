<?php
declare(strict_types = 1);

namespace Bulletpoint\Configuration;

use Klapuch\Configuration;

/**
 * Configuration for whole application
 */
final class ApplicationConfiguration implements Configuration\Source {
	private const LOCAL_CONFIGURATION = __DIR__ . '/config.local.ini';
	private const PRODUCTION_CONFIGURATION = __DIR__ . '/config.production.ini';
	private const SECRET_CONFIGURATION = __DIR__ . '/secrets.ini';
	private const ROUTES = __DIR__ . '/routes.ini';

	public function read(): array {
		return (new Configuration\CachedSource(
			new Configuration\CombinedSource(
				new Configuration\ValidIni(
					new \SplFileInfo(
						self::env() === 'local'
							? self::LOCAL_CONFIGURATION
							: self::PRODUCTION_CONFIGURATION,
					),
				),
				new Configuration\ValidIni(new \SplFileInfo(self::SECRET_CONFIGURATION)),
				new Configuration\NamedSource(
					'ROUTES',
					new Configuration\ValidIni(new \SplFileInfo(self::ROUTES)),
				),
			),
			self::key(),
		))->read();
	}

	private static function env(): string {
		return $_SERVER['BULLETPOINT_ENV'] ?? 'production';
	}

	private static function key(): string {
		return (string) crc32(
			implode(
				',',
				array_map(
					'filemtime',
					[
						self::LOCAL_CONFIGURATION,
						self::PRODUCTION_CONFIGURATION,
						self::SECRET_CONFIGURATION,
						self::ROUTES,
					],
				),
			),
		);
	}
}
