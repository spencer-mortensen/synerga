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

use Synerga\Arguments;
use Synerga\Html5;
use Synerga\Page;

class HeadCommand implements Command
{
	private $page;

	public function __construct(Page $page)
	{
		$this->page = $page;
	}

	public function run(Arguments $arguments)
	{
		$elements = [];

		$this->addTitle($elements);
		$this->addCss($elements);
		$this->addJs($elements);

		if (count($elements) === '') {
			return '';
		}

		return "\n\t" . implode("\n\t", $elements);
	}

	private function addTitle(array &$elements)
	{
		$title = $this->page->getTitle();

		$elements[] = $this->getTitleHtml($title);
	}

	private function getTitleHtml(string $value): string
	{
		$valueHtml = Html5::getText($value);

		return "<title>{$valueHtml}</title>";
	}

	private function addCss(array &$elements)
	{
		$css = $this->page->getCss();

		foreach ($css as $url) {
			$elements[] = $this->getCssHtml($url);
		}
	}

	private function getCssHtml(string $url): string
	{
		$urlHtml = Html5::getAttribute($url);

		return <<<"EOS"
<link href="{$urlHtml}" rel="stylesheet" type="text/css">
EOS;
	}

	private function addJs(array &$elements)
	{
		$js = $this->page->getJs();

		foreach ($js as $url) {
			$elements[] = $this->getJsHtml($url);
		}
	}

	private function getJsHtml(string $url): string
	{
		$urlHtml = Html5::getAttribute($url);

		return <<<"EOS"
<script src="{$urlHtml}" type="text/javascript" defer></script>
EOS;
	}
}
