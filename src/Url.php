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

class Url
{
	/** @var string|null */
	private $baseUrl;

	/** @var string */
	private $path;

	public function __construct($baseUrl, $path)
	{
		$this->baseUrl = $baseUrl;
		$this->path = $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getUrl($path)
	{
		$relativeUrl = self::getRelativeUrl($this->path, $path);

		if ($this->baseUrl === null) {
			return $relativeUrl;
		}

		$absoluteUrl = self::getAbsoluteUrl($this->baseUrl, $path);

		if (strlen($absoluteUrl) < strlen($relativeUrl)) {
			return $absoluteUrl;
		}

		return $relativeUrl;
	}

	private static function getAbsoluteUrl($baseUrl, $path)
	{
		return $baseUrl . $path;
	}

	private static function getRelativeUrl($aPath, $bPath)
	{
		$aAtoms = self::getAtoms($aPath);
		$bAtoms = self::getAtoms($bPath);

		$aCount = count($aAtoms);
		$bCount = count($bAtoms);

		for ($i = 0, $n = min($aCount, $bCount); ($i < $n) && ($aAtoms[$i] === $bAtoms[$i]); ++$i);

		$cAtoms = array_fill(0, $aCount - $i, '..');

		$cTailAtoms = array_slice($bAtoms, $i);

		if (0 < count($cTailAtoms)) {
			$isPage = self::isPagePath($bPath);
			$cAtoms[] = self::getUrlFromAtoms($cTailAtoms, $isPage);
		}

		return self::getUrlFromAtoms($cAtoms, false);
	}

	private static function getAtoms($path)
	{
		$path = rtrim($path, '/');

		if (strlen($path) === 0) {
			return [];
		}

		return explode('/', $path);
	}

	// TODO: this is duplicated in the "Commands/RouterCommand" class
	private static function isPagePath($path)
	{
		return (strlen($path) === 0) || (substr($path, -1) === '/');
	}

	private static function getUrlFromAtoms(array $atoms, $isPage)
	{
		if (count($atoms) === 0) {
			return '.';
		}

		$url = implode('/', $atoms);

		if ($isPage) {
			$url .= '/';
		}

		return $url;
	}
}
