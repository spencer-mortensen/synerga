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
use Synerga\Call;
use Synerga\StringInput;

class Parser
{
	const EXPRESSION = 1;
	const NAME = 2;
	const RIGHT_PARENTHESIS = 3;
	const KEY_VALUE = 4;
	const COLON = 5;
	const RIGHT_BRACE = 6;

	/** @var StringInput */
	private $input;

	/** @var int */
	private $errorPosition;

	/** @var int */
	private $errorExpectation;

	public function parse(StringInput $input, &$expression = null)
	{
		$this->input = $input;
		$this->errorPosition = 0;
		$this->errorExpectation = self::EXPRESSION;

		return $this->readExpression($expression);
	}

	private function readExpression(&$value): bool
	{
		if (
			$this->readCall($value) ||
			$this->readObject($value) ||
			$this->readString($value) ||
			$this->readNumber($value) ||
			$this->readBoolean($value) ||
			$this->readNull($value) ||
			$this->readVariable($value)
		) {
			return true;
		}

		return $this->error(self::EXPRESSION);
	}

	private function readCall(&$call): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->readLeftParenthesis() &&
			$this->readName($name) &&
			$this->readArgumentList($arguments) &&
			$this->readRightParenthesis()
		) {
			$call = new Call($name, $arguments);
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function readLeftParenthesis(): bool
	{
		return $this->input->readRe('\\s*\\(');
	}

	private function readName(&$name): bool
	{
		if ($this->input->readRe('[a-zA-Z0-9_-]+', $name)) {
			return true;
		}

		return $this->error(self::NAME);
	}

	private function readArgumentList(&$arguments): bool
	{
		$arguments = [];

		while (
			$this->input->readRe('\\s*') &&
			$this->readExpression($argument)
		) {
			$arguments[] = $argument;
		}

		return true;
	}

	private function readRightParenthesis(): bool
	{
		if ($this->input->readRe('\\s*\\)')) {
			return true;
		}

		return $this->error(self::RIGHT_PARENTHESIS);
	}

	private function readObject(&$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->readLeftBrace() &&
			$this->readMap($value) &&
			$this->readRightBrace()
		) {
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function readLeftBrace(): bool
	{
		if ($this->input->readRe("{\\s*")) {
			return true;
		}

		return false;
	}

	private function readMap(&$map): bool
	{
		$map = [];

		if ($this->readKeyValue($key, $value)) {
			do {
				$map[$key] = $value;
			} while ($this->readLink($key, $value));
		}

		return true;
	}

	private function readLink(&$key, &$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->readComma() &&
			$this->readKeyValue($key, $value)
		) {
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function readComma(): bool
	{
		if ($this->input->readRe('\\s*,\\s*')) {
			return true;
		}

		return false;
	}

	private function readKeyValue(&$key, &$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->readString($key) &&
			$this->readColon() &&
			$this->readExpression($value)
		) {
			return true;
		}

		$this->input->setPosition($i);
		return $this->error(self::KEY_VALUE);
	}

	private function readColon(): bool
	{
		if ($this->input->readRe('\\s*:\\s*')) {
			return true;
		}

		return $this->error(self::COLON);
	}

	private function readRightBrace(): bool
	{
		if ($this->input->readRe("\\s*}")) {
			return true;
		}

		return $this->error(self::RIGHT_BRACE);
	}

	private function readString(&$value): bool
	{
		if ($this->input->readRe('"(?:[^\\x00-\\x1f"\\\\]|\\\\(?:["\\\\/bfnrt]|u[0-9a-f]{4}))*"', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function readNumber(&$value): bool
	{
		if ($this->input->readRe('-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function readBoolean(&$value): bool
	{
		if ($this->input->readLiteral('false')) {
			$value = false;
			return true;
		}

		if ($this->input->readLiteral('true')) {
			$value = true;
			return true;
		}

		return false;
	}

	private function readNull(&$value): bool
	{
		if ($this->input->readLiteral('null')) {
			$value = null;
			return true;
		}

		return false;
	}

	private function readVariable(&$call): bool
	{
		if ($this->readName($name)) {
			$call = new Call($name, []);
			return true;
		}

		return false;
	}

	private function error(string $code): bool
	{
		$position = $this->input->getPosition();

		if ($this->errorPosition <= $position) {
			$this->errorPosition = $position;
			$this->errorExpectation = $code;
		}

		return false;
	}

	public function getErrorPosition(): int
	{
		return $this->errorPosition;
	}

	public function getErrorExpectation(): int
	{
		return $this->errorExpectation;
	}
}
