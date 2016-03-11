<?php
namespace Bulletpoint\Core\UI;

use Bulletpoint\Core\Http;

final class FlashMessage {
    const MESSAGE = 'flash_message';
    private $session;

    public function __construct(array &$session) {
        $this->session = &$session;
    }

    public function flash(string $message, string $type) {
        $this->session[self::MESSAGE][] = [$type => $message];
    }

    public function read(): array {
    	if (!isset($this->session[self::MESSAGE]))
            return [];
        $messages = $this->session[self::MESSAGE];
        unset($this->session[self::MESSAGE]);
        return $messages;
    }
}