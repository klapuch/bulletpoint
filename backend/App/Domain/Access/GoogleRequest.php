<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Http;
use Klapuch\Http\Response;
use Klapuch\Uri;
use Nette\Utils\Json;

/**
 * Google request with retrieved credentials
 */
final class GoogleRequest implements Http\Request {
	/** @var string */
	private $accessToken;

	public function __construct(string $accessToken) {
		$this->accessToken = $accessToken;
	}

	public function send(): Response {
		$response = (new Http\BasicRequest(
			'GET',
			new Uri\ValidUrl(sprintf('https://www.googleapis.com/oauth2/v3/userinfo?access_token=%s', $this->accessToken)),
		))->send();
		if ($response->code() !== HTTP_OK) {
			throw new \UnexpectedValueException('Error during retrieving Google token.', 0, new \Exception($response->body()));
		}
		return new Http\FakeResponse(
			self::unify($response->body()),
			$response->headers(),
			$response->code(),
		);
	}

	private static function unify(string $body): string {
		$decoded = Json::decode($body, Json::FORCE_ARRAY);
		$decoded['id'] = $decoded['sub'];
		unset($decoded['sub']);
		return Json::encode($decoded);
	}
}
