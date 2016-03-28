<?php
namespace Bulletpoint\Model\Wiki;

final class ConstantInformationSource implements InformationSource {
    private $place;
    private $year;
    private $author;
    private $origin;

    public function __construct(
        string $place,
        $year,
        string $author,
        InformationSource $origin
    ) {
        $this->place = $place;
        $this->year = $year;
        $this->author = $author;
        $this->origin = $origin;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function place(): string {
        return $this->place;
    }

    public function year() {
        return $this->year;
    }

    public function author(): string {
        return $this->author;
    }

    public function edit(string $place, $year, string $author) {
        $this->origin->edit($place, $year, $author);
    }
}