<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Model\Access;

interface Comment {
	public function author(): Access\Identity;
	public function content(): string;
	public function date(): \Datetime;
	public function id(): int;
	public function edit(string $content);
	public function erase();
	public function visible(): bool;
}