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

use SpencerMortensen\Parser\Rule;
use SpencerMortensen\Parser\String\Parser as StringParser;
use SpencerMortensen\Parser\String\Rules;

class Parser extends StringParser
{
	/** @var array */
	private $commandNamespaces;

	/** @var Objects */
	private $objects;

	/** @var Rule */
	private $rule;

	public function __construct()
	{
		$grammar = <<<'EOS'
command: AND commandBegin identifier arguments optionalSpace commandEnd
commandBegin: STRING <:
identifier: RE [a-zA-Z_0-9]+
arguments: MANY argumentSegment 0
argumentSegment: AND space argument
space: RE \s+
argument: OR value command
value: OR null boolean number string
null: STRING null
boolean: RE (?:false|true)
number: RE -?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?
string: RE "(?:[^\x00-\x1f"\\]|\\(?:["\\/bfnrt]|u[0-9a-f]{4}))*"
optionalSpace: RE \s*
commandEnd: STRING :>
EOS;

		$rules = new Rules($this, $grammar);
		$this->rule = $rules->getRule('command');
	}

	public function parse(&$input)
	{
		$output = $this->run($this->rule, $input);

		$position = $this->getPosition();
		$input = substr($input, $position);

		return $output;
	}

	public function getCommand(array $matches)
	{
		$name = $matches[1];
		$arguments = $matches[2];

		return new Command($name, $arguments);
	}

	public function getArgumentSegment(array $segment)
	{
		return $segment[1];
	}

	public function getValue($json)
	{
		return json_decode($json, true);
	}
}
