<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Filesystem;

final class File implements Filesystem\File {
	private $name;
	private $type;
	private $size;
	private $content;
	private $location;

	public function __construct(
		string $name = null,
		string $type = null,
		int $size = null,
		string $content = null,
		string $location = null
	){
		$this->name = $name;
		$this->type = $type;
		$this->size = $size;
		$this->content = $content;
		$this->location = $location;
	}

	public function name(): string {
		return $this->name;
	}

	public function type(): string {
		return $this->type;
	}

	public function size(): int {
		return $this->size;
	}

	public function content(): string {
		return $this->content;
	}

	public function location(): string {
		return $this->location;
	}
}