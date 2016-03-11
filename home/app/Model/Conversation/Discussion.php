<?php
namespace Bulletpoint\Model\Conversation;

interface Discussion {
	public function id(): int;
	public function contribute(string $content);
	public function contributions(): \Iterator;
}