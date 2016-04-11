<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Wiki};

final class Bulletpoints implements Wiki\Bulletpoints {
    private $id;
    private $count;

    public function __construct(array $id = [], int $count = null) {
        $this->id = $id;
        $this->count = $count;
    }

    public function add(
        string $content,
        Wiki\Document $document,
        Wiki\InformationSource $source
    ) {
		
	}

	public function iterate(): \Iterator {
        foreach($this->id as $id)
            yield new Bulletpoint($id);
	}

    public function count(): int {
        return $this->count;
    }
}