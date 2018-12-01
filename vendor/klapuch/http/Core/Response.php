<?php
declare(strict_types = 1);
namespace Klapuch\Http;

interface Response {
	/**
	 * Body of the response
	 * @throws \Exception
	 * @return string
	 */
	public function body(): string;

	/**
	 * Headers of the response
	 * @return array
	 */
	public function headers(): array;

	/**
	 * Code of the response in range 1xx - 5xx
	 * @throws \Exception
	 * @return int
	 */
	public function code(): int;
}
