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

class ParserException extends Exception
{
	/** @var string */
	private $text;

	/** @var int */
	private $position;

	/** @var int */
	private $expectation;

	public function __construct(string $text, int $position, int $expectation)
	{
		$message = $this->newMessage($text, $position, $expectation);
		parent::__construct($message);

		$this->text = $text;
		$this->position = $position;
		$this->expectation = $expectation;
	}

	private function newMessage(string $text, int $position, int $expectation)
	{
		switch ($expectation) {
			default:
				return 'Expected expression';

			case Parser::NAME:
				return 'Expected a name';

			case Parser::RIGHT_PARENTHESIS:
				return 'Expected ")"';

			case Parser::KEY_VALUE:
				return 'Expected a key/value pair';

			case Parser::COLON:
				return 'Expected ":"';

			case Parser::RIGHT_BRACE:
				return 'Expected ";"';

			case Parser::END:
				return 'Extra fluff found at the end';
		}

		return 'hey';
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function getPosition(): int
	{
		return $this->position;
	}

	public function getExpectation(): int
	{
		return $this->expectation;
	}
}
