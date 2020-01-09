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

class PersistentArray
{
	/** @var Data */
	private $data;

	/** @var string */
	private $path;

	/** @var null */
	private $storedValue;

	/** @var null */
	private $activeValue;

	public function __construct(Data $data, string $path)
	{
		$this->data = $data;
		$this->path = $path;
		$this->storedValue = null;
		$this->activeValue = null;
	}

	public function &link()
	{
		if ($this->storedValue === null) {
			$this->activeValue = $this->readData();
			$this->storedValue = $this->activeValue;
		}

		return $this->activeValue;
	}

	public function __destruct()
	{
		if ($this->storedValue !== $this->activeValue) {
			$this->writeData($this->activeValue);
		}
	}

	private function readData(): array
	{
		$contents = $this->data->read($this->path);

		if ($contents === null) {
			return [];
		}

		$value = json_decode($contents, true);

		if (!is_array($value)) {
			return [];
		}

		return $value;
	}

	public function writeData(array $value)
	{
		$contents = json_encode($value);

		$this->data->write($this->path, $contents);
	}
}
