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

namespace Synerga\Interpreter;

use Exception;
use Synerga\Evaluator;

class Interpreter
{
	/** @var Parser */
	private $parser;

	/** @var Evaluator */
	private $evaluator;

	public function __construct(Parser $parser, Evaluator $evaluator)
	{
		$this->parser = $parser;
		$this->evaluator = $evaluator;
	}

	public function run(string $code): string
	{
		$command = $this->parser->parse($code);
		return $this->evaluator->evaluate($command);
	}

	public function interpret(string $input): string
	{
		$iBegin = 0;

		ob_start();

		while (true) {
			$iEnd = strpos($input, '<:', $iBegin);

			if ($iEnd === false) {
				echo substr($input, $iBegin);
				break;
			}

			echo substr($input, $iBegin, $iEnd - $iBegin);

			$iBegin = $iEnd + 2;
			$iEnd = strpos($input, ':>', $iBegin);

			if ($iEnd === false) {
				// TODO: error handling
				throw new Exception("Missing ':>'");
			}

			$command = substr($input, $iBegin, $iEnd - $iBegin);
			$command = ltrim($command);

			echo $this->run($command);
			$iBegin = $iEnd + 2;
		}

		return ob_get_clean();
	}
}
