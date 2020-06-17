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

class Data
{
	/** @var string */
	private $dataDirectory;

	public function __construct($dataDirectory)
	{
		$this->dataDirectory = $dataDirectory;
	}

	// TODO: use the filesystem class
	public function read($path)
	{
		$filePath = $this->getFilePath($path);

		if (!is_file($filePath)) {
			return null;
		}

		$contents = file_get_contents($filePath);

		if (!is_string($contents)) {
			return null;
		}

		return $contents;
	}

	public function write($path, $contents)
	{
		$filePath = $this->getFilePath($path);

		if (!file_exists($filePath)) {
			$directoryPath = dirname($filePath);

			if (!file_exists($directoryPath)) {
				mkdir($directoryPath, 0777, true);
			}
		}

		file_put_contents($filePath, $contents);
	}

	public function send($path)
	{
		$filePath = $this->getFilePath($path);

		readfile($filePath);
	}

	public function exists($path)
	{
		$filePath = $this->getFilePath($path);
		$directoryPath = dirname($filePath);

		return is_dir($directoryPath);
	}

	public function getSizeBytes($path)
	{
		$filePath = $this->getFilePath($path);

		// TODO: may return false (and generate an accompanying E_WARNING)

		return filesize($filePath);
	}

	public function mtime($path)
	{
		$filePath = $this->getFilePath($path);

		$mtime = filemtime($filePath);

		if (!is_integer($mtime)) {
			$mtime = null;
		}

		return $mtime;
	}

	private function getFilePath($path)
	{
		return rtrim("{$this->dataDirectory}/{$path}", '/');
	}
}
