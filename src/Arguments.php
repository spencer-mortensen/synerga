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

use InvalidArgumentException;

class Arguments
{
	private $evaluator;
	private $arguments;

	public function __construct(Evaluator $evaluator, array $arguments)
	{
		$this->evaluator = $evaluator;
		$this->arguments = $arguments;
	}

	// TODO: check for the existence of an argument (without evaluating it)?
	// TODO: get the number of arguments?

	public function getArgument(int $i)
	{
		if (!array_key_exists($i, $this->arguments)) {
			throw new InvalidArgumentException("Missing argument {$i}");
		}

		$argument = $this->arguments[$i] ?? null;

		if ($argument instanceof Call) {
			$argument = $this->evaluator->evaluate($argument);
		}

		return $argument;
	}

	// TODO: catch this exception in the "Evaluator" class and add the command name...
	public function getBoolean(int $i): bool
	{
		$argument = $this->getArgument($i);

		if (!is_bool($argument)) {
			// TODO:
			throw new InvalidArgumentException("Argument {$i} should be a boolean. FOOL!");
		}

		return $argument;
	}

	public function getInteger(int $i): int
	{
		$argument = $this->getArgument($i);

		if (!is_int($argument)) {
			// TODO:
			throw new InvalidArgumentException("Argument {$i} should be an integer.");
		}

		return $argument;
	}

	public function getFloat(int $i): float
	{
		$argument = $this->getArgument($i);

		if (!is_float($argument)) {
			// TODO:
			throw new InvalidArgumentException("Argument {$i} should float. At least, that's what the flight attendant said.");
		}

		return $argument;
	}

	public function getString(int $i): string
	{
		$argument = $this->getArgument($i);

		if (!is_string($argument)) {
			$argumentText = json_encode($argument);

			// TODO:
			throw new InvalidArgumentException("Argument {$i} should be a string, but turned out be: {$argumentText}.");
		}

		return $argument;
	}

	public function getArray(int $i): array
	{
		$argument = $this->getArgument($i);

		if (!is_array($argument)) {
			// TODO:
			throw new InvalidArgumentException("Argument {$i} ISN'T an array? WTF! I thought it was!");
		}

		return $argument;
	}

	public function count(): int
	{
		return count($this->arguments);
	}
}
