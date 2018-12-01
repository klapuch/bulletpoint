<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class CookieExtension implements Extension {
	private const PROPRIETARIES = ['SameSite'];
	private $settings;

	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	public function improve(): void {
		header(static::raw($this->settings));
	}

	/**
	 * The raw cookie header
	 * @param array $settings
	 * @return string
	 */
	private static function raw(array $settings): string {
		$cookie = current(preg_grep('~^Set-Cookie: ~', headers_list()));
		if ($cookie === false)
			return '';
		$matches = array_intersect_ukey(
			array_flip(self::PROPRIETARIES),
			$settings,
			'strcasecmp'
		);
		$headers = array_combine(
			array_flip($matches),
			array_intersect_ukey($settings, $matches, 'strcasecmp')
		);
		return rtrim(
			trim(
				sprintf(
					'%s; %s',
					$cookie,
					implode(
						';',
						array_map(
							function(string $field, string $value): string {
								return sprintf('%s=%s', $field, $value);
							},
							array_keys($headers),
							$headers
						)
					)
				)
			),
			';'
		);
	}
}