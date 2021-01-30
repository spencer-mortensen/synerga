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

use Synerga\ErrorHandling\Exceptions\ArgumentException;
use Synerga\ErrorHandling\Exceptions\CallException;
use Throwable;

class Evaluator
{
	/** @var array */
	private $factory;

	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	public function evaluate($value)
	{
		if ($value instanceof Call) {
			return $this->evaluateCall($value);
		}

		if (is_array($value)) {
			return $this->evaluateArray($value);
		}

		return $value;
	}

	private function evaluateCall(Call $call)
	{
		try {
			$command = $this->getCommand($call);
			$arguments = $this->getArguments($call);
			return $command->run($arguments);
		} catch (ArgumentException $argumentException) {
			$class = get_class($command);
			throw new CallException($class, $argumentException);
		}
	}

	private function getCommand(Call $call)
	{
		$callName = $call->getName();
		$commandName = $this->getCommandName($callName);
		return $this->factory->$commandName;
	}

	private function getCommandName(string $name): string
	{
		if (strpos($name, '-') !== false) {
			$atoms = explode('-', $name);
			$name = $atoms[0];

			for ($i = 1, $n = count($atoms); $i < $n; ++$i) {
				$name .= ucfirst($atoms[$i]);
			}
		}

		return "{$name}Command";
	}

	private function getArguments(Call $call)
	{
		$arguments = $call->getArguments();
		return new Arguments($this, $arguments);
	}

	private function evaluateArray(array $array)
	{
		$result = [];

		foreach ($array as $key => $value) {
			$result[$key] = $this->evaluate($value);
		}

		return $result;
	}
}
