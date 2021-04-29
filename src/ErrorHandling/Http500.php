<?php

namespace Synerga\ErrorHandling;

use Synerga\Documents\Response;

class Http500
{
	public function send(string $content)
	{
		ob_end_clean();

		$code = '500 Internal Server Error';
		$headers = [];

		$response = new Response($code, $headers, $content);
		$response->send();
		exit(1);
	}
}
