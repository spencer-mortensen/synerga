<?php

namespace Synerga\ErrorHandling;

class Http500
{
	public function send(string $content)
	{
		$contentLength = strlen($content);

		ob_end_clean();

		header("HTTP/1.1 500 Internal Server Error");
		header("Content-Length: {$contentLength}");
		echo $content;

		exit(1);
	}
}
