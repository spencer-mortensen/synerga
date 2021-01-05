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

class Parser
{
	const EXPRESSION = 1;
	const NAME = 2;
	const RIGHT_PARENTHESIS = 3;
	const KEY_VALUE = 4;
	const COLON = 5;
	const RIGHT_BRACE = 6;
	const END = 7;

	/** @var StringInput */
	private $input;

	/** @var int */
	private $errorPosition;

	/** @var int */
	private $errorExpectation;

	public function parse(string $text)
	{
		$this->input = new StringInput($text);

		if (
			$this->getExpression($expression) &&
			$this->getEnd()
		) {
			return $expression;
		}

		throw new ParserException($text, $this->errorPosition, $this->errorExpectation);
	}

	private function getExpression(&$value): bool
	{
		if (
			$this->getCall($value) ||
			$this->getObject($value) ||
			$this->getString($value) ||
			$this->getNumber($value) ||
			$this->getBoolean($value) ||
			$this->getNull($value) ||
			$this->getVariable($value)
		) {
			return true;
		}

		return $this->error(self::EXPRESSION);
	}

	private function getCall(&$call): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->getLeftParenthesis() &&
			$this->getName($name) &&
			$this->getArgumentList($arguments) &&
			$this->getRightParenthesis()
		) {
			$call = new Call($name, $arguments);
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function getLeftParenthesis(): bool
	{
		return $this->input->getRe('\\s*\\(');
	}

	private function getName(&$name): bool
	{
		if ($this->input->getRe('[a-zA-Z0-9_-]+', $name)) {
			return true;
		}

		return $this->error(self::NAME);
	}

	private function getArgumentList(&$arguments): bool
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

	private function getRightParenthesis(): bool
	{
		if ($this->input->getRe('\\s*\\)')) {
			return true;
		}

		return $this->error(self::RIGHT_PARENTHESIS);
	}

	private function getObject(&$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->getLeftBrace() &&
			$this->getMap($value) &&
			$this->getRightBrace()
		) {
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function getLeftBrace(): bool
	{
		if ($this->input->getRe("{\\s*")) {
			return true;
		}

		return false;
	}

	private function getMap(&$map): bool
	{
		$map = [];

		if ($this->getKeyValue($key, $value)) {
			do {
				$map[$key] = $value;
			} while ($this->getLink($key, $value));
		}

		return true;
	}

	private function getLink(&$key, &$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->getComma() &&
			$this->getKeyValue($key, $value)
		) {
			return true;
		}

		$this->input->setPosition($i);
		return false;
	}

	private function getComma(): bool
	{
		if ($this->input->getRe('\\s*,\\s*')) {
			return true;
		}

		return false;
	}

	private function getKeyValue(&$key, &$value): bool
	{
		$i = $this->input->getPosition();

		if (
			$this->getString($key) &&
			$this->getColon() &&
			$this->getExpression($value)
		) {
			return true;
		}

		$this->input->setPosition($i);
		return $this->error(self::KEY_VALUE);
	}

	private function getColon(): bool
	{
		if ($this->input->getRe('\\s*:\\s*')) {
			return true;
		}

		return $this->error(self::COLON);
	}

	private function getRightBrace(): bool
	{
		if ($this->input->getRe("\\s*}")) {
			return true;
		}

		return $this->error(self::RIGHT_BRACE);
	}

	private function getString(&$value): bool
	{
		if ($this->input->getRe('"(?:[^\\x00-\\x1f"\\\\]|\\\\(?:["\\\\/bfnrt]|u[0-9a-f]{4}))*"', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getNumber(&$value): bool
	{
		if ($this->input->getRe('-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?', $json)) {
			$value = json_decode($json, true);
			return true;
		}

		return false;
	}

	private function getBoolean(&$value): bool
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

	private function getNull(&$value): bool
	{
		if ($this->input->getLiteral('null')) {
			$value = null;
			return true;
		}

		return false;
	}

	private function getVariable(&$call): bool
	{
		if ($this->getName($name)) {
			$call = new Call($name, []);
			return true;
		}

		return false;
	}

	private function getEnd(): bool
	{
		if (
			$this->input->getRe("\\s*") &&
			$this->input->getEnd()
		) {
			return true;
		}

		return $this->error(self::END);
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
}
