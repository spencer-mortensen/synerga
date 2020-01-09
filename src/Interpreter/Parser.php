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

use Synerga\Call;

class Parser
{
	/** @var StringInput */
	private $input;

	public function parse(StringInput $input, Call &$call = null)
	{
		$this->input = $input;

		return $this->getExpression($call);
	}

	private function getExpression(&$value)
	{
		return $this->getNull($value) ||
			$this->getBoolean($value) ||
			$this->getNumber($value) ||
			$this->getString($value) ||
			$this->getObject($value) ||
			$this->getVariable($value) ||
			$this->getCall($value);
	}

	private function getNull(&$value)
	{
		if ($this->input->getLiteral('null')) {
			$value = null;
			return true;
		}

		return false;
	}

	private function getBoolean(&$value)
	{
		if ($this->input->getLiteral('false')) {
			$value = false;
			return true;
		}

		if ($this->input->getLiteral('true')) {
			$value = true;
			return true;
		}

		return false;
	}

	private function getNumber(&$value)
	{
		if ($this->input->getRe('-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getString(&$value)
	{
		if ($this->input->getRe('"(?:[^\\x00-\\x1f"\\\\]|\\\\(?:["\\\\/bfnrt]|u[0-9a-f]{4}))*"', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getObject(&$value)
	{
		return $this->input->getRe("{\\s*") &&
			$this->getMap($value) &&
			$this->input->getRe("\\s*}");
	}

	private function getMap(&$map)
	{
		$map = [];

		if ($this->getKeyValue($key, $value)) {
			do {
				$map[$key] = $value;
			} while (
				$this->input->getRe('\\s*,\\s*') &&
				$this->getKeyValue($key, $value)
			);
		}

		return true;
	}

	private function getKeyValue(&$key, &$value)
	{
		return $this->getString($key) &&
			$this->input->getRe('\\s*:\\s*') &&
			$this->getExpression($value);
	}

	private function getVariable(&$call)
	{
		if ($this->input->getRe('[a-zA-Z0-9_-]+', $name)) {
			$call = new Call($name, []);
			return true;
		}

		return false;
	}

	private function getCall(&$call)
	{
		if (
			$this->input->getRe('\\s*\\(') &&
			$this->input->getRe('[a-zA-Z0-9_-]+', $name) &&
			$this->getArgumentList($arguments) &&
			$this->input->getRe('\\s*\\)')
		) {
			$call = new Call($name, $arguments);
			return true;
		}

		return false;
	}

	private function getArgumentList(&$arguments)
	{
		$arguments = [];

		while (
			$this->input->getRe('\\s*') &&
			$this->getExpression($argument)
		) {
			$arguments[] = $argument;
		}

		return true;
	}
}
