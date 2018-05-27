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

class Scanner
{
	/** @var Parser */
	private $parser;

	public function __construct(Parser $parser, Evaluator $evaluator)
	{
		$this->parser = $parser;
		$this->evaluator = $evaluator;
	}

	public function scan($input)
	{
		while ($this->seek($input, $output)) {
			$command = $this->parser->parse($input);
			$output .= $this->evaluator->run($command);
		}

		return $output;
	}

	private function seek(&$input, &$output)
	{
		if (strlen($input) === 0) {
			return false;
		}

		$i = strpos($input, '<:');

		if (is_int($i)) {
			$output .= substr($input, 0, $i);
			$input = substr($input, $i);
			return true;
		} else {
			$output .= $input;
			$input = '';
			return false;
		}
	}
}
