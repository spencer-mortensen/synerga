<?php

namespace Synerga\ErrorHandling;

use Synerga\Html5;

class Error500
{
	public function send(string $message, array $context = null)
	{
		$content = $this->getContent($message, $context);
		$contentLength = strlen($content);

		header("HTTP/1.1 500 Internal Server Error");
		header("Content-Length: {$contentLength}");
		echo $content;

		exit(1);
	}

	private function getContent(string $message, array $context = null): string
	{
		$bodyHtml = $this->getBodyHtml($message, $context);

		return <<<"EOS"
<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Error</title>
	<link href="https://fonts.googleapis.com/css2?family=Dosis:wght@500&display=swap" rel="stylesheet">
	<style type="text/css">
* {
	margin: 0;
	padding: 0;
}

body {
	display: inline-block;
	font-family: 'Dosis', sans-serif;
	font-size: 1.25em;
	left: 50%;
	position: absolute;
	top: 50%;
	transform: translate(-50%, -50%);
}

h1 {
	color: #000;
	display: block;
	font-weight: normal;
	font-size: 2em;
	line-height: 1em;
	margin: 0 auto;
	text-align: center;
}

h1 .type {
	display: block;
	font-size: 1.5em;
	line-height: 1em;
	text-transform: uppercase;
}

h1 .code {
	display: block;
	font-size: 3em;
	line-height: 1.125em;
}

p {
	color: #333;
	font-style: italic;
	line-height: 1.5em;
	margin-top: .375em;
	text-align: left;
}

dl {
	display: block;
	line-height: 1.5em;
	text-align: left;
	white-space: nowrap;
}

dt {
	display: inline-block;
	text-align: right;
	width: 3em;
}

dt::before {
	content: '•';
	padding-right: .125em;
}

dt::after {
	content: ':';
	padding-right: .25em;
}

dd {
	display: inline-block;
}
	</style>
</head>

<body>
<h1><span class="type">Error</span> <span class="code">500</span></h1>

<p>{$bodyHtml}</p>
</body>

</html>
EOS;
	}

	private function getBodyHtml(string $message, array $context = null): string
	{
		$messageHtml = '<p>' . Html5::getText($message) . '</p>';
		$contextHtml = $this->getDlHtml($context);

		return "{$messageHtml}{$contextHtml}";
	}

	private function getDlHtml(array $map): string
	{
		$rows = [];

		foreach ($map as $key => $value) {
			$keyHtml = Html5::getText($key);
			$valueHtml = Html5::getText($value);

			$rows[] = "<dl><dt>{$keyHtml}</dt><dd>{$valueHtml}</dd></dl>";
		}

		return implode('', $rows);
	}
}
