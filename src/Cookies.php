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

class Cookies
{
	/** @var array */
	private $options;

	public function __construct(array $options = null)
	{
		if ($options === null) {
			$options = $this->getDefaultOptions();
		}

		$this->options = $options;
	}

	private function getDefaultOptions()
	{
		return [
			'path' => '/',
			'domain' => $_SERVER['HTTP_HOST'],
			'secure' => false,
			'httponly' => true
		];
	}

	public function get($key, &$value = null)
	{
		if (!isset($_COOKIE[$key])) {
			return false;
		}

		$value = $_COOKIE[$key];
		return true;
	}

	public function set($key, $value, $life = null)
	{
		if (is_int($life)) {
			$expires = time() + $life;
		} else {
			$expires = null;
		}

		setcookie($key, $value, $expires, $this->options['path'], $this->options['domain'], $this->options['secure'], $this->options['httponly']);
	}

	public function unset($key)
	{
		$value = '';
		$expires = time() - 1;

		setcookie($key, $value, $expires, $this->options['path'], $this->options['domain'], $this->options['secure'], $this->options['httponly']);
		unset($_COOKIE[$key]);
	}
}
