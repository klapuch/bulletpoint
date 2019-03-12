<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Http;
use Klapuch\Http\Response;

/**
 * OAuth request with retrieved credentials
 */
final class OAuthRequest implements Http\Request {
	/** @var string */
	private $provider;

	/** @var string */
	private $accessToken;

	public function __construct(string $provider, string $accessToken) {
		$this->provider = $provider;
		$this->accessToken = $accessToken;
	}

	public function send(): Response {
		if ($this->provider === 'facebook') {
			$request = new FacebookRequest($this->accessToken);
		} elseif ($this->provider === 'google') {
			$request = new GoogleRequest($this->accessToken);
		} else {
			throw new \UnexpectedValueException(sprintf('Provider "%s" is not known', $this->provider));
		}
		return $request->send();
	}
}
