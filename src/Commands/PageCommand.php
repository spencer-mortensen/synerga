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
use Synerga\Data;
use Synerga\Interpreter;
use Synerga\Page;

class PageCommand implements Command
{
	private $data;
	private $interpreter;
	private $page;

	public function __construct(Data $data, Interpreter $interpreter, Page $page)
	{
		$this->data = $data;
		$this->interpreter = $interpreter;
		$this->page = $page;
	}

	public function run(Arguments $arguments)
	{
		for ($i = 0, $n = $arguments->count(); $i < $n; ++$i) {
			$path = $arguments->getString($i);
			$this->setPage($path);
		}

		return $this->page->getHtml();
	}

	private function setPage(string $path)
	{
		$this->setTitle($path);
		$this->addCss($path);
		$this->addJs($path);
		$this->setHtml($path);

	}

	private function setTitle(string $path)
	{
		$title = $this->getString("{$path}.title/");

		if ($title === null) {
			return;
		}

		$this->page->setTitle($title);
	}

	private function getString(string $path)
	{
		$contents = $this->data->read($path);

		if ($contents === null) {
			return null;
		}

		$value = trim($this->interpreter->interpret($contents));

		if (strlen($value) === 0) {
			return null;
		}

		return $value;
	}

	private function addCss(string $path)
	{
		$css = $this->getArray("{$path}.css/");

		if ($css === null) {
			return;
		}

		foreach ($css as $url) {
			$this->page->addCss($url);
		}
	}

	private function getArray($path)
	{
		$text = $this->getString($path);

		if ($text === null) {
			return null;
		}

		return array_map('trim', explode("\n", $text));
	}

	private function addJs(string $path)
	{
		$js = $this->getArray("{$path}.js/");

		if ($js === null) {
			return;
		}

		foreach ($js as $url) {
			$this->page->addJs($url);
		}
	}

	private function setHtml(string $path)
	{
		$html = $this->getString("{$path}.html/");

		if ($html === null) {
			return;
		}

		$this->page->setHtml($html);
	}
}
