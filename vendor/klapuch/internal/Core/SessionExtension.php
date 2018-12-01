<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class SessionExtension implements Extension {
	private const TIMER = '_timer',
		DEFAULT_BREAK = 20;
	private $settings;
	private $break;

	public function __construct(array $settings, int $break = self::DEFAULT_BREAK) {
		$this->settings = $settings;
		$this->break = $break;
	}

	public function improve(): void {
		if (session_status() === PHP_SESSION_NONE)
			session_start($this->settings);
		if ($this->elapsed($this->break))
			session_regenerate_id(true);
		$_SESSION[self::TIMER] = time();
	}

	private function elapsed(int $break): bool {
		return isset($_SESSION[self::TIMER])
		&& (time() - $_SESSION[self::TIMER]) > $break;
	}
}