<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

final class ConstantDocument implements Document {
	private $title;
	private $description;
	private $author;
	private $date;
	private $source;
	private $origin;

	public function __construct(
		string $title,
		string $description,
		Access\Identity $author,
		\Datetime $date,
		InformationSource $source,
		Document $origin
	) {
		$this->title = $title;
		$this->description = $description;
		$this->author = $author;
		$this->date = $date;
		$this->source = $source;
		$this->origin = $origin;
	}

	public function author(): Access\Identity {
		return $this->author;
	}

	public function description(): string {
		return $this->description;
	}

	public function title(): string {
		return $this->title;
	}

	public function date(): \Datetime {
		return $this->date;
	}

	public function source(): InformationSource {
		return $this->source;
	}

	public function id(): int {
		return $this->origin->id();
	}

	public function edit(string $title, string $description) {
		$this->origin->edit($title, $description);
	}
}