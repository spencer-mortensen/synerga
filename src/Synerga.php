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

use ErrorException;
use Synerga\Commands\Command;
use SpencerMortensen\Parser\String\Parser;
use SpencerMortensen\Parser\String\Rules;

class Synerga extends Parser
{
	/** @var array */
	private $commandNamespaces;

	/** @var Objects */
	private $objects;

	/** @var string */
	private $input;

	public function __construct(array $commandNamespaces)
	{
		$this->commandNamespaces = $commandNamespaces; // e.g. array("Example\\Application\\Commands", "Synerga\\Commands")
		$this->objects = new Objects();
		$this->objects->set('synerga', $this);

		$grammar = <<<'EOS'
expression: AND commands text
commands: MANY commandSegment 0
commandSegment: AND text command
text: RE .*?(?=<:|$)
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
		$rule = $rules->getRule('expression');

		parent::__construct($rule);
	}

	public function run($input)
	{
		if (!is_string($input)) {
			return null;
		}

		$this->input = $input;
		$output = '';

		$nodes = parent::parse($input);

		foreach ($nodes as $node) {
			if (is_object($node)) {
				/** @var Command $node */
				$value = $node->run();
			} else {
				$value = $node;
			}

			$output .= $value;
		}

		return $output;
	}

	public function getExpression(array $parts)
	{
		list($commands, $text) = $parts;

		if ($text !== null) {
			$commands[] = $text;
		}

		return $commands;
	}

	public function getCommands(array $segments)
	{
		if (count($segments) === 0) {
			return array();
		}

		$commands = call_user_func_array('array_merge', $segments);
		$commands = array_filter($commands, array($this, 'isNotNull'));
		return array_values($commands);
	}

	public function isNotNull($input)
	{
		return $input !== null;
	}

	public function getText($text)
	{
		if (strlen($text) === 0) {
			return null;
		}

		return $text;
	}

	public function getCommand(array $parts)
	{
		$name = $parts[1];
		$arguments = $parts[2];

		$className = ucfirst($name) . 'Command';

		foreach ($this->commandNamespaces as $namespace) {
			$class = "\\{$namespace}\\{$className}";

			if (class_exists($class, true)) {
				return new $class($this->objects, $arguments);
			}
		}

		throw new ErrorException("Unknown command: {$name}");
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
