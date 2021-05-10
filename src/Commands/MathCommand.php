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

namespace Synerga\Commands;

use Exception;
use Synerga\Arguments;
use Synerga\Html;
use Synerga\Page;
use Synerga\Url;

class MathCommand implements Command
{
	private $url;
	private $page;
	private $html;

	public function __construct(Url $url, Page $page, Html $html)
	{
		$this->url = $url;
		$this->page = $page;
		$this->html = $html;
	}

	public function run(Arguments $arguments)
	{
		$mathScriptHtml = $this->getMathScriptHtml();

		$this->page->addHeadElement('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.13.9/dist/katex.min.css" integrity="sha384-r/BYDnh2ViiCwqZt5VJVWuADDic3NnnTIEOv4hOh05nSfB6tjWpKmn1kUHOVkMXc" crossorigin="anonymous">');
		$this->page->addHeadElement('<script defer src="https://cdn.jsdelivr.net/npm/katex@0.13.9/dist/katex.min.js" integrity="sha384-zDIgORxjImEWftZXZpWLs2l57fMX9B3yWFPN5Ecabe211Hm5ZG/OIz2b07DYPUcH" crossorigin="anonymous"></script>');
		$this->page->addHeadElement($mathScriptHtml);

		$tex = $arguments->getString(0);
		$display = $arguments->getOptionalBoolean(1) ?? false;

		return $this->getMathElementHtml($tex, $display);
	}

	private function getMathScriptHtml()
	{
		$path = '.config/apps/math/math.min.js';
		$url = $this->url->getUrl($path);
		$urlHtml = $this->html->encode($url);

		return "<script src=\"{$urlHtml}\" defer></script>";
	}

	private function getMathElementHtml(string $tex, bool $display)
	{
		$texHtml = $this->html->encode($tex);

		if ($display) {
			$tagName = 'div';
		} else {
			$tagName = 'i';
		}

		return "<{$tagName} data-tex=\"{$texHtml}\"></{$tagName}>";
	}
}
