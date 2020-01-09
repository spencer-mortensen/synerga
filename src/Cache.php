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

use Memcached;

class Cache
{
	private $memcached;

	public function __construct(Memcached $memcached)
	{
		$this->memcached = $memcached;
	}

	public function get(string $name, &$value = null)
	{
		$value = $this->memcached->get($name);

		if ($value !== false) {
			return true;
		}

		$status = $this->memcached->getResultCode();

		return ($status !== Memcached::RES_NOTFOUND);
	}

	public function set(string $name, $value)
	{
		$this->memcached->set($name, $value);
	}
}
