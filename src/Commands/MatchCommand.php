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

use Synerga\Arguments;
use Synerga\Variables;

class MatchCommand implements Command
{
	private $variables;

	public function __construct(Variables $variables)
	{
		$this->variables = $variables;
	}

	public function run(Arguments $arguments)
	{
		$expression = $arguments->getString(0);
		$input = $arguments->getString(1);

		$pattern = self::getPattern($expression);

		if (preg_match($pattern, $input, $match) !== 1) {
			return false;
		}

		if (2 < $arguments->count()) {
			$name = $arguments->getString(2);
			$value = $match[1] ?? null;
			$this->variables->set($name, $value);
		}

		return true;
	}

	private static function getPattern(string $expression)
	{
		$delimiter = "\x03";
		$modifiers = 'XDs';

		return $delimiter . $expression . $delimiter . $modifiers;
	}
}
