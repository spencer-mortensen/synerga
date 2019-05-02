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

namespace Synerga;

class Head
{
	private $css;
	private $js;

	public function __construct()
	{
		$this->css = [];
		$this->js = [];
	}

	public function getHtml(): string
	{
		$elements = [];

		foreach ($this->css as $path) {
			$elements[] = $this->getCssHtml($path);
		}

		foreach ($this->js as $path) {
			$elements[] = $this->getJsHtml($path);
		}

		if (0 < count($elements)) {
			return "\t" . implode("\n\t", $elements) . "\n";
		}

		return '';
	}

	public function addCssPath(string $path)
	{
		$this->css[$path] = $path;
	}

	public function addJsPath(string $path)
	{
		$this->js[$path] = $path;
	}

	private function getCssHtml(string $path): string
	{
		$pathHtml = Html5::attribute($path);

		return <<<"EOS"
<link href="{$pathHtml}" rel="stylesheet" type="text/css">
EOS;
	}

	private function getJsHtml(string $path): string
	{
		$pathHtml = Html5::attribute($path);

		return <<<"EOS"
<script src="{$pathHtml}" type="text/css"></script>
EOS;
	}
}
