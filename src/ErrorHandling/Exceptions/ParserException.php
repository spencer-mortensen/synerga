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

namespace Synerga\ErrorHandling\Exceptions;

use Exception;
use Synerga\Interpreter\Parser;

class ParserException extends Exception
{
	/** @var int */
	private $expectation;

	public function __construct(int $expectation)
	{
		$message = $this->newMessage($expectation);

		parent::__construct($message);

		$this->expectation = $expectation;
	}

	private function newMessage(int $expectation): string
	{
		switch ($expectation) {
			case Parser::EXPRESSION:
				return 'Expected an expression';

			case Parser::NAME:
				return 'Expected a name';

			case Parser::RIGHT_PARENTHESIS:
				return 'Missing “)”';

			case Parser::KEY_VALUE:
				return 'Expected a key/value pair';

			case Parser::COLON:
				return 'Missing “:”';

			case Parser::RIGHT_BRACE:
				return 'Missing “}”';

			default:
				return '';
		}
	}

	public function getExpectation(): int
	{
		return $this->expectation;
	}
}
