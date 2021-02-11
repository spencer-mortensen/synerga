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
use Synerga\Html;
use Synerga\Page;

class HeadCommand implements Command
{
	/** @var Page */
	private $page;

	/** @var Html */
	private $html;

	public function __construct(Page $page, Html $html)
	{
		$this->page = $page;
		$this->html = $html;
	}

	public function run(Arguments $arguments)
	{
		$title = null;
		$css = [];
		$js = [];

		$head = $this->page->getHead();
		$head = array_reverse($head);

		foreach ($head as $i => $html) {
			if ($this->isCss($html)) {
				$css[] = $html;
				unset($head[$i]);
			} elseif ($this->isJs($html)) {
				$js[] = $html;
				unset($head[$i]);
			} elseif ($this->isTitle($html)) {
				$title = $html;
			}
		}

		if ($title === null) {
			$title = $this->getTitleHtml();
		}

		return $this->getHeadHtml($title, $css, $js, $head);
	}

	private function isCss(string $html): bool
	{
		return (strncmp($html, '<link', 5) === 0) &&
			(strpos($html, 'rel="stylesheet"') !== false);
	}

	private function isJs(string $html): bool
	{
		return strncmp($html, '<script', 7) === 0;
	}

	private function isTitle(string $html): bool
	{
		return strncmp($html, '<title', 6) === 0;
	}

	private function getTitleHtml(): string
	{
		$title = $this->page->getTitle();
		$titleHtml = $this->html->encode($title);

		return "<title>{$titleHtml}</title>";
	}

	private function getHeadHtml(string $title, array $css, array $js, array $head): string
	{
		return "\n\t" . $title .
			$this->getArrayHtml($css) .
			$this->getArrayHtml($js) .
			$this->getArrayHtml($head);
	}

	private function getArrayHtml(array $elements): string
	{
		if (count($elements) === 0) {
			return '';
		}

		return "\n\t" . implode("\n\t", $elements);
	}
}
