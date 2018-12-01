<?php
declare(strict_types = 1);
namespace Klapuch\Log;

interface Severity {
	public const INFO = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR = 'ERROR';

	/**
	 * Level of the severity
	 * @return string
	 */
	public function level(): string;
}