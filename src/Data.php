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

	public function __construct(string $dataDirectory)
	{
		$this->dataDirectory = $dataDirectory;
	}

	// TODO: use the filesystem class
	public function read(string $path)
	{
		$filePath = $this->getAbsolutePath($path);

		if (!is_file($filePath)) {
			return null;
		}

		$contents = file_get_contents($filePath);

		if (!is_string($contents)) {
			return null;
		}

		return $contents;
	}

	public function write(string $path, string $contents)
	{
		$filePath = $this->getAbsolutePath($path);

		if (!file_exists($filePath)) {
			$directoryPath = dirname($filePath);

			if (!file_exists($directoryPath)) {
				mkdir($directoryPath, 0777, true);
			}
		}

		file_put_contents($filePath, $contents);
	}

	public function send(string $path)
	{
		$filePath = $this->getAbsolutePath($path);

		readfile($filePath);
	}

	public function isDirectory(string $path): bool
	{
		$filePath = $this->getAbsolutePath($path);

		return is_dir($filePath);
	}

	public function isFile(string $path): bool
	{
		$filePath = $this->getAbsolutePath($path);

		return is_file($filePath);
	}

	public function getChildren(string $path): array
	{
		$directoryPath = rtrim("{$this->dataDirectory}/{$path}", '/');
		$directory = opendir($directoryPath);

		$children = [];

		for ($child = readdir($directory); $child !== false; $child = readdir($directory)) {
			if (($child === '.') || ($child === '..')) {
				continue;
			}

			$children[] = $child;
		}

		return $children;
	}

	public function getSizeBytes(string $path)
	{
		$filePath = $this->getAbsolutePath($path);

		// TODO: may return false (and generate an accompanying E_WARNING)

		return filesize($filePath);
	}

	public function mtime(string $path)
	{
		$filePath = $this->getAbsolutePath($path);

		$mtime = filemtime($filePath);

		if (!is_integer($mtime)) {
			$mtime = null;
		}

		return $mtime;
	}

	private function getAbsolutePath(string $path)
	{
		return rtrim("{$this->dataDirectory}/{$path}", '/');
	}
}
