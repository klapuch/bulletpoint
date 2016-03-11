<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Email;

final class Message implements Email\Message {
	private $recipient;
	private $subject;
	private $content;
	private $sender;

	public function __construct(
		string $recipient = null,
		string $subject = null,
		string $content = null,
		string $sender = null
	) {
		$this->sender = $sender;
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->content = $content;
	}

	public function sender(): string {
		return $this->sender;
	}

	public function recipient(): string {
		return $this->recipient;
	}

	public function subject(): string {
		return $this->subject;
	}

	public function content(): string {
		return $this->content;
	}
}