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

use Synerga\Exceptions\ArgumentException;

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

	public function count(): int
	{
		return count($this->arguments);
	}

	public function getBoolean(int $i): bool
	{
		$argument = $this->getArgument($i);

		if (!is_bool($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_BOOLEAN);
		}

		return $argument;
	}

	public function getInteger(int $i): int
	{
		$argument = $this->getArgument($i);

		if (!is_int($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_INTEGER);
		}

		return $argument;
	}

	public function getFloat(int $i): float
	{
		$argument = $this->getArgument($i);

		if (!is_float($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_FLOAT);
		}

		return $argument;
	}

	public function getString(int $i): string
	{
		$argument = $this->getArgument($i);

		if (!is_string($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_STRING);
		}

		return $argument;
	}

	public function getArray(int $i): array
	{
		$argument = $this->getArgument($i);

		if (!is_array($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_ARRAY);
		}

		return $argument;
	}

	public function getOptionalBoolean(int $i)
	{
		$argument = $this->getOptionalArgument($i);

		if (($argument !== null) && !is_bool($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_BOOLEAN);
		}

		return $argument;
	}

	public function getOptionalInteger(int $i)
	{
		$argument = $this->getOptionalArgument($i);

		if (($argument !== null) && !is_int($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_INTEGER);
		}

		return $argument;
	}

	public function getOptionalFloat(int $i)
	{
		$argument = $this->getOptionalArgument($i);

		if (($argument !== null) && !is_float($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_FLOAT);
		}

		return $argument;
	}

	public function getOptionalString(int $i)
	{
		$argument = $this->getOptionalArgument($i);

		if (($argument !== null) && !is_string($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_STRING);
		}

		return $argument;
	}

	public function getOptionalArray(int $i)
	{
		$argument = $this->getOptionalArgument($i);

		if (($argument !== null) && !is_array($argument)) {
			throw new ArgumentException($i, $argument, ArgumentException::TYPE_ARRAY);
		}

		return $argument;
	}

	public function getOptionalArgument(int $i)
	{
		$argument = $this->arguments[$i] ?? null;

		if ($argument instanceof Call) {
			$argument = $this->evaluator->evaluate($argument);
		}

		return $argument;
	}
}
