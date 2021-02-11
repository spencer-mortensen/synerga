<?php

namespace Synerga\ErrorHandling\Pages;

use Synerga\ErrorHandling\Exceptions\ParserException;
use Synerga\Html;
use Synerga\Interpreter\Parser;

class CodePage
{
	/** @var Html */
	private $html;

	public function __construct(Html $html)
	{
		$this->html = $html;
	}

	public function getHtml(string $title, string $bodyHtml, string $text, int $position, string $expectation): string
	{
		$bodyHtml .= $this->getCodeHtml($text, $position, $expectation);
		$pageCss = $this->getPageCss();

		$page = new SitePage($this->html);
		return $page->getBody($title, $bodyHtml, $pageCss);
	}

	private function getPageCss(): string
	{
		return <<<"EOS"
code b {
	display: inline-block;
	font-weight: normal;
	height: 1em;
	position: relative;
}

code b::before {
	content: "";
	border: .5em solid transparent;
	border-bottom-color: #fdd;
	border-top: 0;
	left: -.5em;
	margin-top: .75em;
	position: absolute;
	top: -.45em;
}

code b::after {
	background-color: #fdd;
	border-radius: .375em;
	content: attr(data-expectation);
	left: -1.25em;
	margin-top: .75em;
	padding: .75em;
	position: absolute;
	top: 100%;
	white-space: nowrap;
}
EOS;
	}

	private function getCodeHtml(string $text, int $position, string $expectation): string
	{
		$pre = substr($text, 0, $position);
		$post = substr($text, $position);

		$preHtml = $this->html->encode($pre);
		$postHtml = $this->html->encode($post);
		$expectationHtml = $this->html->encode($expectation);

		return <<<"EOS"
<code>{$preHtml}<b data-expectation="{$expectationHtml}"></b>{$postHtml}</code>
EOS;
	}
}
