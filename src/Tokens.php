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

class Tokens
{
	/** @var Data */
	private $data;

	/** @var string */
	private $path;

	public function __construct(Data $data)
	{
		$this->data = $data;
		$this->path = '.config/tokens';
	}

	public function get($key, &$user = null, &$isSingleUse = null)
	{
		$file = new PersistentArray($this->data, $this->path);
		$tokens = &$file->link();

		$this->unsetExpiredTokens($tokens);

		if (!isset($tokens[$key])) {
			return false;
		}

		list($user, $isSingleUse) = $tokens[$key];
		return true;
	}

	private function unsetExpiredTokens(array &$tokens)
	{
		$currentTime = time();

		foreach ($tokens as $key => $value) {
			list($user, $isSingleUse, $expirationTime) = $value;

			if (is_int($expirationTime) && ($expirationTime <= $currentTime)) {
				unset($tokens[$key]);
			}
		}
	}

	public function set($key, string $user, bool $isSingleUse, int $expirationTime = null)
	{
		$file = new PersistentArray($this->data, $this->path);
		$tokens = &$file->link();

		$tokens[$key] = [$user, $isSingleUse, $expirationTime];
	}

	public function unset($key)
	{
		$file = new PersistentArray($this->data, $this->path);
		$tokens = &$file->link();

		unset($tokens[$key]);
	}
}
