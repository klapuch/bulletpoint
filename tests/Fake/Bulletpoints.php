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

	public function iterate(): array {
        $bulletpoints = [];
        foreach($this->id as $id)
            $bulletpoints[] = new Bulletpoint($id);
        return $bulletpoints;
	}

    public function count(): int {
        return $this->count;
    }
}