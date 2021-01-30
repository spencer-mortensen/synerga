<?php

namespace Synerga\ErrorHandling\Pages;

use Synerga\ErrorHandling\Exceptions\ParserException;
use Synerga\Html;
use Synerga\Interpreter\Parser;

class SitePage
{
	/** @var Html */
	private $html;

	public function __construct(Html $html)
	{
		$this->html = $html;
	}

	public function getHtml(string $title, string $bodyHtml, string $pageCss = null): string
	{
		$css = $this->getSiteCss();

		if ($pageCss !== null) {
			$css .= "\n\n" . $pageCss;
		}

		$titleHtml = $this->html->encode($title);

		return <<<"EOS"
<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{$titleHtml}</title>
	<link href="https://fonts.googleapis.com/css2?family=Dosis:wght@500&amp;display=swap" rel="stylesheet">
	<style>
{$css}
	</style>
</head>

<body>
{$bodyHtml}
</body>

</html>
EOS;
	}

	private function getSiteCss(): string
	{
		return <<<"EOS"
* {
	margin: 0;
	padding: 0;
}

body {
	display: inline-block;
	font-family: 'Dosis', sans-serif;
	font-size: 1.25em;
	margin: 12.5%;
}

h1 {
	color: #000;
	display: block;
	font-weight: normal;
	font-size: 2em;
	line-height: 1em;
	padding-bottom: .75em;
}

code {
	font-family: monospace;
	line-height: 1em;
	-moz-tab-size: 4;
	tab-size: 4;
	white-space: pre-wrap;
}
EOS;
	}
}
