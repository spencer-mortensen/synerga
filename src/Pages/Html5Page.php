<?php

/**
 * Copyright (C) 2017 Spencer Mortensen
 *
 * This file is part of Synerga.
 *
 * Synerga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Synerga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Synerga. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Spencer Mortensen <smortensen@datto.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2017 Spencer Mortensen
 */

namespace Synerga\Pages;

use Synerga\Html5;

class Html5Page implements Page
{
	private $body;

	public function __construct(Page $page)
	{
		$head = $page->getHead();

		$headHtml = $this->newHeadHtml($head);
		$bodyHtml = $page->getBody();

		$this->body = $this->newBody($headHtml, $bodyHtml);
	}

	public function getHead(): array
	{
		return [];
	}

	public function getBody(): string
	{
		return $this->body;
	}

	private function newHeadHtml(array $head)
	{
		$titleHtml = Html5::text($head['title']);

		$lines = [
			'<meta charset="utf-8">',
			"<title>{$titleHtml}</title>"
		];

		foreach ($head['css'] as $path) {
			$lines[] = $this->newCssHtml($path);
		}

		foreach ($head['js'] as $path) {
			$lines[] = $this->newJsHtml($path);
		}

		$lines[] = '<link href="favicon.ico" rel="shortcut icon" type="image/x-icon">';
		return "\t" . implode("\n\t", $lines);
	}

	private function newCssHtml(string $path)
	{
		$pathHtml = Html5::attribute($path);

		return <<<"EOS"
<link href="{$pathHtml}" rel="stylesheet" type="text/css">
EOS;
	}

	private function newJsHtml(string $path)
	{
		$pathHtml = Html5::attribute($path);

		return <<<"EOS"
<script src="{$pathHtml}" type="text/javascript"></script>
EOS;
	}

	private function newBody(string $headHtml, string $bodyHtml)
	{
		return <<<"EOS"
<!DOCTYPE html>

<html lang="en">

<head>
{$headHtml}
</head>

<body>
{$bodyHtml}
</body>

</html>
EOS;
	}
}
