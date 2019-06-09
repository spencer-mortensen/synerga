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
use Synerga\Html5;
use Synerga\Url;

class MenuCommand implements Command
{
	/** @var Url */
	private $url;

	public function __construct(Url $url)
	{
		$this->url = $url;
	}

	public function run(Arguments $arguments): string
	{
		$map = $arguments->getArray(0);
		$selectedPath = $this->url->getPath();

		return $this->getUlHtml($map, $selectedPath);
	}

	private function getUlHtml(array $map, string $selectedPath): string
	{
		$lis = [];

		foreach ($map as $path => $text) {
			$isSelected = ($path === $selectedPath);
			$lis[] = $this->getLiHtml($path, $text, $isSelected);
		}

		return '<ul>' . implode('', $lis) . '</ul>';
	}

	private function getLiHtml(string $path, string $text, bool $isSelected): string
	{
		$textHtml = Html5::text($text);

		if ($isSelected) {
			return "<li class=\"here\"><a>{$textHtml}</a></li>";
		}

		$url = $this->url->getUrl($path);
		$urlHtml = Html5::attribute($url);

		return "<li><a href=\"{$urlHtml}\">{$textHtml}</a></li>";
	}
}
