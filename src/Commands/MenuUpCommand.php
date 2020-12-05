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
use Synerga\Data;
use Synerga\Html5;
use Synerga\Interpreter\Interpreter;
use Synerga\Url;

class MenuUpCommand implements Command
{
	/** @var Url */
	private $url;

	/** @var Data */
	private $data;

	public function __construct(Url $url, Data $data, Interpreter $interpreter)
	{
		$this->url = $url;
		$this->data = $data;
		$this->interpreter = $interpreter;
	}

	public function run(Arguments $arguments): string
	{
		$url = $this->getParentUrl();
		$title = $this->getText("{$url}.page/.title/");

		return $this->getAHtml($url, $title);
	}

	private function getParentUrl(): string
	{
		$url = $this->url->getPath();
		$atoms = $this->getAtoms($url);

		array_pop($atoms);

		return implode('/', $atoms) . '/';
	}

	private function getAtoms(string $url): array
	{
		$url = rtrim($url, '/');

		if (strlen($url) === 0) {
			return [];
		}

		return explode('/', $url);
	}

	private function getText(string $path): string
	{
		$text = $this->data->read($path);

		if ($text === null) {
			return '';
		}

		return $this->interpreter->interpret($text);
	}

	private function getAHtml(string $path, string $text): string
	{
		$url = $this->url->getUrl($path);
		$urlHtml = Html5::getAttribute($url);
		$textHtml = Html5::getText($text);

		return "<a href=\"{$urlHtml}\">{$textHtml}</a>";
	}
}
