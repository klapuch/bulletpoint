<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Wiki};

final class Bulletpoints implements Wiki\Bulletpoints {
    private $id;

    public function __construct(array $id = []) {
        $this->id = $id;
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
        return 0;
    }
}