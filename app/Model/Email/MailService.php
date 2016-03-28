<?php
namespace Bulletpoint\Model\Email;

final class MailService implements PostService {
    public function send(Message $message) {
        mail(
            $message->recipient(),
            $message->subject(),
            $message->content(),
            implode("\r\n", $this->headers($message))
        );
    }

    private function headers(Message $message): array {
        return [
            'From: ' . $message->sender(),
            'MIME-Version: 1.0',
            'Date: ' . date('r'),
            'Content-type: text/html; charset=UTF-8',
            'X-Priority: 1',
        ];
    }
}