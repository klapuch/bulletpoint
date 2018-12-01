<?php
declare(strict_types = 1);
namespace Klapuch\Http;

use Klapuch\Uri;

/**
 * Basic HTTP request
 */
final class BasicRequest implements Request {
	private $method;
	private $uri;
	private $options;
	private $body;

	public function __construct(
		string $method,
		Uri\Uri $uri,
		array $options = [],
		string $body = ''
	) {
		$this->method = $method;
		$this->uri = $uri;
		$this->options = $options;
		$this->body = $body;
	}

	public function send(): Response {
		return new RawResponse(...$this->response($this->body));
	}

	/**
	 * Response given from the requested source
	 * @param string $fields
	 * @throws \Exception
	 * @return array
	 */
	private function response(string $fields): array {
		$curl = curl_init();
		try {
			curl_setopt_array(
				$curl,
				[
					CURLOPT_URL => $this->uri->reference(),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_AUTOREFERER => true,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_CUSTOMREQUEST => strtoupper($this->method),
					CURLOPT_POSTFIELDS => $fields,
				] + $this->options
			);
			$body = curl_exec($curl);
			if ($body === false)
				throw new \UnexpectedValueException(curl_error($curl));
			stream_context_set_default(
				[
					'http' => [
						'method' => $this->method,
						'header' => implode(
							"\r\n",
							$this->options[CURLOPT_HTTPHEADER] ?? []
						),
					],
				]
			);
			return [get_headers($this->uri->reference()), $body];
		} finally {
			curl_close($curl);
		}
	}
}
