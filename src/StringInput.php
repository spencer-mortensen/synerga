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

class StringInput
{
	/** @var string */
	private $input;

	/** @var integer */
	private $position;

	public function __construct(string $input = null, int $position = 0)
	{
		$this->input = $input;
		$this->position = $position;
	}

	public function getLiteral(string $string): bool
	{
		if (strlen($this->input) <= $this->position) {
			return false;
		}

		$length = strlen($string);

		if (substr_compare($this->input, $string, $this->position, $length) !== 0) {
			return false;
		}

		$this->position += $length;
		return true;
	}

	public function getRe(string $expression, &$output = null): bool
	{
		if (strlen($this->input) <= $this->position) {
			return false;
		}

		$pattern = "\x03{$expression}\x03XADs";

		if (preg_match($pattern, $this->input, $matches, 0, $this->position) !== 1) {
			return false;
		}

		$output = (count($matches) === 1) ? $matches[0] : $matches;
		$this->position += strlen($matches[0]);
		return true;
	}

	public function getInput(): string
	{
		return $this->input;
	}

	public function setInput(string $input)
	{
		$this->input = $input;
	}

	public function getPosition(): int
	{
		return $this->position;
	}

	public function setPosition(int $position)
	{
		$this->position = $position;
	}
}
