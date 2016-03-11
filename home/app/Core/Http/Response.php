<?php
namespace Bulletpoint\Core\Http;

final class Response {
	private $address;

	public function __construct(Address $address) {
		$this->address = $address;
	}

	public function redirect(string $url = null, int $code = 302) {
		$redirectUrl = $this->address->basename() . $url;
		if($url === null)
			$redirectUrl .= implode('/', $this->address->pathname());
		header('Location: ' . $redirectUrl, true, $code);
		exit;
	}

	public function redirectUrl(string $url, int $code = 302) {
		header('Location: ' . $url, true, $code);
		exit;
	}

	public function redirectReferer(string $fallback = null, int $code = 302) {
		if(isset($_SERVER['HTTP_REFERER']))
			$this->redirectUrl($_SERVER['HTTP_REFERER'], $code);
		elseif(strlen($fallback))
			$this->redirect($fallback, $code);
		$this->redirect('', $code);
	}
}