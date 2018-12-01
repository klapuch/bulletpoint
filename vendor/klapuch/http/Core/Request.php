<?php
declare(strict_types = 1);
namespace Klapuch\Http;

interface Request {
	/**
	 * Send the request and receive response as a feedback
	 * @throws \Exception
	 * @return \Klapuch\Http\Response
	 */
	public function send(): Response;
}
