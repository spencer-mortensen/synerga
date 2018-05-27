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

use Synerga\Data;
use Synerga\File;
use Synerga\Page;
use Synerga\Url;

class RouterCommand
{
	/** @var Data */
	private $data;

	/** @var Url */
	private $url;

	/** @var Page */
	private $page;

	/** @var File */
	private $file;

	public function __construct(Data $data, Url $url, Page $page, File $file)
	{
		$this->data = $data;
		$this->url = $url;
		$this->page = $page;
		$this->file = $file;
	}

	public function run()
	{
		list($routing, $defaultTheme) = $this->getRouting();
		$path = $this->url->getPath();

		$theme = &$routing[$path];

		if (isset($theme)) {
			$this->page->send($theme);
			return;
		}

		$pathType = $this->url->getType();

		if ($pathType === 'page') {
			$this->page->send($defaultTheme);
		} else {
			$this->file->send($path);
		}
	}

	private function getRouting()
	{
		$contents = $this->data->read('.config/routing');

		if ($contents === null) {
			return null;
		}

		$settings = json_decode($contents, true);

		if (!is_array($settings)) {
			return null;
		}

		$routing = &$settings[0];

		if (!is_array($routing)) {
			return null;
		}

		$defaultTheme = &$settings[1];

		if (!is_string($defaultTheme)) {
			return null;
		}

		return array($routing, $defaultTheme);
	}
}
