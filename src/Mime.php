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

class Mime
{
	/** @var Data */
	private $data;

	public function __construct(Data $data)
	{
		$this->data = $data;
	}

	public function getType($path)
	{
		$defaultMimeType = 'application/octet-stream';

		$extension = self::getExtension($path);

		$class = $this->getExtensionClass($extension);

		if ($class === null) {
			return $defaultMimeType;
		}

		$type = $this->getClassType($class);

		if ($type === null) {
			return $defaultMimeType;
		}

		return $type;
	}

	private static function getExtension($path)
	{
		$extension = pathinfo($path, PATHINFO_EXTENSION);

		if (strlen($extension) === 0) {
			return null;
		}

		return $extension;
	}

	private function getExtensionClass($extension)
	{
		$path = ".config/mime/class/{$extension}";
		return $this->data->read($path);
	}

	private function getClassType($class)
	{
		$path = ".config/mime/type/{$class}";
		return $this->data->read($path);
	}
}
