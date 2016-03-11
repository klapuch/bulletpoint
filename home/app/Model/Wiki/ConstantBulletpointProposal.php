<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

final class ConstantBulletpointProposal implements BulletpointProposal {
	private $author;
	private $date;
	private $source;
	private $content;
	private $document;
	private $origin;

	public function __construct(
		Access\Identity $author,
		\Datetime $date,
		InformationSource $source,
		string $content,
		Document $document,
		BulletpointProposal $origin
	) {
		$this->author = $author;
		$this->date = $date;
		$this->source = $source;
		$this->content = $content;
		$this->document = $document;
		$this->origin = $origin;
	}

	public function author(): Access\Identity {
		return $this->author;
	}

	public function content(): string {
		return $this->content;
	}

	public function source(): InformationSource {
		return $this->source;
	}

	public function id(): int {
		return $this->origin->id();
	}

	public function date(): \Datetime {
		return $this->date;
	}

	public function document(): Document {
		return $this->document;
	}

	public function edit(string $content) {
		$this->origin->edit($content);
	}

	public function accept(): Bulletpoint {
		return $this->origin->accept();
	}

	public function reject(string $reason = null) {
		$this->origin->reject($reason);
	}
}