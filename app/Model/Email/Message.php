<?php
namespace Bulletpoint\Model\Email;

interface Message {
    public function sender(): string;
    public function recipient(): string;
    public function subject(): string;
    public function content(): string;
}