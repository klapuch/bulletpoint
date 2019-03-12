<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Http;
use Klapuch\Http\Response;
use Klapuch\Uri;

/**
 * Facebook request with retrieved credentials
 */
final class FacebookRequest implements Http\Request {
	/** @var string */
	private $accessToken;

	public function __construct(string $accessToken) {
		$this->accessToken = $accessToken;
	}

	public function send(): Response {
		$response = (new Http\BasicRequest(
			'GET',
			new Uri\ValidUrl(sprintf('https://graph.facebook.com/v2.3/me?fields=email&access_token=%s', $this->accessToken))
		))->send();
		if ($response->code() !== HTTP_OK) {
			throw new \UnexpectedValueException('Error during retrieving Facebook token.', 0, new \Exception($response->body()));
		}
		return $response;
	}
}
