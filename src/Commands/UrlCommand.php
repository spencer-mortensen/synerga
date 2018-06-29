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

use Synerga\Scanner;
use Synerga\Url;

class UrlCommand
{
	/** @var Scanner */
	private $scanner;

	/** @var Url */
	private $url;

	public function __construct(Scanner $scanner, Url $url)
	{
		$this->scanner = $scanner;
		$this->url = $url;
	}

	public function run($path, $isAbsolute = null)
	{
		$path = $this->scanner->scan($path);
		return $this->url->getUrl($path, $isAbsolute);
	}
}
