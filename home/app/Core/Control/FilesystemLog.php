<?php
namespace Bulletpoint\Core\Control;

use Bulletpoint\Core\Filesystem;

final class FilesystemLog implements Log {
	const EOL = "\r\n";
	private $path;

	public function __construct(Filesystem\Path $path) {
		$this->path = $path;
	}

	public function write(array $errors) {
		if($errors) {
			file_put_contents(
				$this->path->full(),
				$this->message($errors),
				FILE_APPEND | LOCK_EX
			);
		}
	}

	public function read(): string {
		return file_get_contents($this->path->full());
	}

	private function message(array $errors): string {
		return $this->formattedMessage([
			$this->datetime(),
			$this->error($errors)
		]);
	}

	private function formattedMessage(array $messages): string {
		return implode(
			self::EOL,
			array_map(function($message) {
				return $message;
		}, $messages)) . self::EOL . str_repeat('_', 80) . self::EOL;
	}

	private function datetime(): string {
		return 'DATETIME: ' . current(new \Datetime);
	}

	private function error(array $errors): string {
		return 'ERROR: ' . implode(
			self::EOL,
			array_map(function ($error, $type) {
				return strtoupper($type) . ': ' . $error;
		}, $errors, array_keys($errors)));
	}
}