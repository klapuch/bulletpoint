<?php
namespace Bulletpoint\Core\Security;

use Bulletpoint\Exception;
use Bulletpoint\Core\Http;

final class CsrfProtection {
    private $session;
    private $request;
    const KEY = 'csrf_protection';

    public function __construct(Http\Session $session, Http\Request $request) {
        $this->session = $session;
        $this->request = $request;
    }

    public function defend() {
        if(!$this->isTokenRecognized())
            $this->timeout();
    }

    public function protection(): string {
        return $this->session[self::KEY] ?? $this->createToken();
    }

    public function key(): string {
        return self::KEY;
    }

    private function isTokenRecognized(): bool {
        return isset($this->session[self::KEY]) && $this->areSame();
    }

    private function areSame(): bool {
        return hash_equals(
            (string)$this->session[self::KEY],
            $this->token()
        );
    }

    private function token(): string {
        if($this->request->post(self::KEY))
            return $this->request->post(self::KEY);
        elseif($this->request->get(self::KEY))
            return $this->request->get(self::KEY);
        $this->timeout();
    }

    private function timeout() {
        throw new Exception\CsrfException('Timeout');
    }

    private function createToken(): string {
        return bin2hex(random_bytes(20));
    }
}