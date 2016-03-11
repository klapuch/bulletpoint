<?php
namespace Bulletpoint\Core\Http;

final class Request {
	private $get;
	private $post;
	private $address;

	public function __construct(array $get, array $post, Address $address) {
		$this->get = $get;
		$this->post = $post;
		$this->address = $address;
	}

	public function get($name = null) {
		return $this->coalesce($this->get, $name);
	}

	public function post($name = null) {
		$post = $this->coalesce($this->post, $name);
		if((array)$post === $post)
			return array_map('trim', $post);
		return trim($post);
	}

	public function address(): Address {
		return $this->address;
	}

	private function coalesce(array $storage, $name) {
		if($name === null)
			return $storage;
		return $storage[$name] ?? null;
	}
}