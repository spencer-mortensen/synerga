<?php

namespace Synerga\ErrorHandling\Pages;

use Synerga\Html;

class GenericErrorPage
{
	/** @var Html */
	private $html;

	public function __construct(Html $html)
	{
		$this->html = $html;
	}

	public function getHtml(string $message, array $context = null): string
	{
		$title = 'Error 500';
		$pageCss = $this->getPageCss();
		$bodyHtml = $this->getBodyHtml($message, $context);

		$page = new SitePage($this->html);
		return $page->getBody($title, $bodyHtml, $pageCss);
	}

	private function getPageCss(): string
	{
		return <<<'EOS'
body {
	left: 50%;
	margin: 0;
	position: absolute;
	top: 50%;
	transform: translate(-50%, -50%);
}

h1 {
	margin: 0 auto;
	padding-bottom: 0;
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
	padding-left: 3em;
	position: relative;
	text-align: left;
	white-space: nowrap;
}

dt {
	display: inline-block;
	left: 0;
	position: absolute;
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
EOS;
	}

	private function getBodyHtml(string $message, array $context = null): string
	{
		$messageHtml = $this->html->encode($message);
		$contextHtml = $this->getDlHtml($context);

		return <<<"EOS"
<h1><span class="type">Error</span> <span class="code">500</span></h1>

<p>{$messageHtml}</p>{$contextHtml}
EOS;
	}

	private function getDlHtml(array $map): string
	{
		$rows = [];

		foreach ($map as $key => $value) {
			$keyHtml = $this->html->encode($key);
			$valueHtml = $this->html->encode($value);

			$rows[] = "<dl><dt>{$keyHtml}</dt><dd>{$valueHtml}</dd></dl>";
		}

		return implode('', $rows);
	}
}
