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

class Parser
{
	public function parse(StringInput $input, Call &$call = null)
	{
		return $this->getCall($input, $call);
	}

	private function getCall(StringInput $input, &$call)
	{
		if (
			$input->getLiteral('<:') &&
			$input->getRe('[a-zA-Z0-9_-]+', $name) &&
			$this->getArguments($input, $arguments) &&
			$input->getRe('\\s*:>')
		) {
			$call = new Call($name, $arguments);
			return true;
		}

		return false;
	}

	private function getArguments(StringInput $input, &$arguments)
	{
		$arguments = [];

		while (
			$input->getRe('\\s+') &&
			$this->getValue($input, $argument)
		) {
			$arguments[] = $argument;
		}

		return true;
	}

	private function getValue(StringInput $input, &$value)
	{
		return $this->getNull($input, $value) ||
			$this->getBoolean($input, $value) ||
			$this->getNumber($input, $value) ||
			$this->getString($input, $value) ||
			$this->getObject($input, $value) ||
			$this->getCall($input, $value);
	}

	private function getNull(StringInput $input, &$value)
	{
		if ($input->getLiteral('null')) {
			$value = null;
			return true;
		}

		return false;
	}

	private function getBoolean(StringInput $input, &$value)
	{
		if ($input->getLiteral('false')) {
			$value = false;
			return true;
		}

		if ($input->getLiteral('true')) {
			$value = true;
			return true;
		}

		return false;
	}

	private function getNumber(StringInput $input, &$value)
	{
		if ($input->getRe('-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getString(StringInput $input, &$value)
	{
		if ($input->getRe('"(?:[^\\x00-\\x1f"\\\\]|\\\\(?:["\\\\/bfnrt]|u[0-9a-f]{4}))*"', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getObject(StringInput $input, &$value)
	{
		return $input->getRe("{\\s*") &&
			$this->getMap($input, $value) &&
			$input->getRe("\\s*}");
	}

	private function getMap(StringInput $input, &$map)
	{
		$map = [];

		if ($this->getKeyValue($input, $key, $value)) {
			do {
				$map[$key] = $value;
			} while (
				$input->getRe('\\s*,\\s*') &&
				$this->getKeyValue($input, $key, $value)
			);
		}

		return true;
	}

	private function getKeyValue(StringInput $input, &$key, &$value)
	{
		return $this->getString($input, $key) &&
			$input->getRe('\\s*:\\s*') &&
			$this->getValue($input, $value);
	}
}
