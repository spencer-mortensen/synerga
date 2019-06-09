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

class Page
{
	private $title;
	private $css;
	private $js;
	private $html;

	public function __construct()
	{
		$this->title = '';
		$this->css = [];
		$this->js = [];
		$this->html = '';
	}

	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function addCss(string $url)
	{
		$this->css[$url] = $url;
	}

	public function getCss()
	{
		return $this->css;
	}

	public function addJs(string $url)
	{
		$this->js[$url] = $url;
	}

	public function getJs()
	{
		return $this->js;
	}

	public function setHtml(string $html)
	{
		$this->html = $html;
	}

	public function getHtml(): string
	{
		return $this->html;
	}
}
