<?php
namespace Bulletpoint\Model\Email;

final class MailMessage implements Message {
    private $origin;

    public function __construct(Message $origin) {
        $this->origin = $origin;
    }

    public function sender(): string {
        return $this->origin->sender();
    }

    public function recipient(): string {
        return $this->origin->recipient();
    }

    public function subject(): string {
        return substr(
            iconv_mime_encode('Subject', $this->origin->subject()),
            strlen('Subject: ')
        );
    }

    public function content(): string {
        return wordwrap($this->origin->content(), 70, "\r\n");
    }
}