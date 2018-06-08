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

use InvalidArgumentException;
use Synerga\Evaluator;

class IfCommand
{
	/** @var Evaluator */
	private $evaluator;

	public function __construct(Evaluator $evaluator)
	{
		$this->evaluator = $evaluator;
	}

	public function run()
	{
		$arguments = func_get_args();

		$n = count($arguments) - 1;

		for ($i = 0; $i < $n; ++$i) {
			$antecedent = $this->getValue($arguments[$i]);

			if (!is_bool($antecedent)) {
				throw new InvalidArgumentException();
			}

			++$i;

			if ($antecedent) {
				return $this->getValue($arguments[$i]);
			}
		}

		$argument = $arguments[$i];
		return $this->getValue($argument);
	}

	private function getValue($argument)
	{
		if (is_object($argument)) {
			return $this->evaluator->run($argument);
		}

		return $argument;
	}
}
