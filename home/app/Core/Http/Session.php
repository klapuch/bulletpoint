<?php
namespace Bulletpoint\Core\Http;

final class Session implements \ArrayAccess {
	private $session;

	public function __construct(array &$session) {
		$this->session = &$session;
	}

    public function offsetSet($offset, $value) {
        if($offset === null)
            $this->session[] = $value;
        else
            $this->session[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->session[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->session[$offset]);
    }

    public function offsetGet($offset) {
        return $this->session[$offset] ?? null;
    }
}