<?php
declare(strict_types = 1);

namespace Bulletpoint\Response;

use Klapuch\Application;
use Klapuch\Output;

/**
 * Error for JSON format
 */
final class JsonError implements Application\Response {
	private const HEADERS = ['content-type' => 'application/json; charset=utf8'];
	private const CODES = [400, 599],
		DELEGATE = 0;

	private \Throwable $error;

	/** @var mixed[] */
	private array $headers;

	private int $status;

	public function __construct(
		\Throwable $error,
		array $headers = [],
		int $status = self::DELEGATE
	) {
		$this->error = $error;
		$this->headers = $headers;
		$this->status = $status;
	}

	public function body(): Output\Format {
		return new Output\Json(['message' => $this->text($this->error)]);
	}

	public function headers(): array {
		return self::HEADERS + array_change_key_case($this->headers);
	}

	public function status(): int {
		$choice = (int) $this->error->getCode() ?: $this->status;
		return in_array($choice, range(...self::CODES), true)
			? $choice
			: HTTP_BAD_REQUEST;
	}

	private function text(\Throwable $error): string {
		return htmlspecialchars($error->getMessage(), ENT_XHTML) ?: t('error.unknown');
	}
}
