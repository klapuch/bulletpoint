<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Conversation;
use Bulletpoint\Model\Access;

final class Comment implements Conversation\Comment {
	private $id;

	public function __construct(int $id) {
		$this->id = $id;
	}

	public function author(): Access\Identity {

	}
	public function content(): string {

	}
	public function date(): \Datetime {

	}
	public function id(): int {
		return $this->id;
	}
	public function edit(string $content) {

	}
	public function erase() {

	}
	public function visible(): bool {

	}
}