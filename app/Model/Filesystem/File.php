<?php
namespace Bulletpoint\Model\Filesystem;

interface File {
    public function name(): string;
    public function type(): string;
    public function size(): int;
    public function content(): string;
    public function location(): string;
}