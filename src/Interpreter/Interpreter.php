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
use Synerga\ErrorHandling\Exceptions\EvaluationException;
use Synerga\ErrorHandling\Exceptions\FileException;
use Synerga\ErrorHandling\Exceptions\ParserException;
use Synerga\Evaluator;
use Synerga\StringInput;
use Throwable;

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

	public function run(string $code)
	{
		$input = new StringInput($code);

		if (!$this->parser->parse($input, $expression)) {
			$code = $input->getInput();
			$position = $this->parser->getErrorPosition();
			$expectation = $this->parser->getErrorExpectation();

			throw new ParserException($code, $position, $expectation);
		}

		$input->readRe("\\s*");

		if (!$input->readEnd()) {
			$code = $input->getInput();
			$position = $input->getPosition();
			$expectation = -1;

			throw new ParserException($code, $position, $expectation);
		}

		return $this->evaluator->evaluate($expression);
	}

	public function interpret(string $text): string
	{
		$input = new StringInput($text);
		$output = '';

		$iBegin = 0;

		while (true) {
			$iEnd = strpos($text, '<:', $iBegin);

			if ($iEnd === false) {
				$output .= substr($text, $iBegin);
				break;
			}

			$output .= substr($text, $iBegin, $iEnd - $iBegin);

			$input->setPosition($iEnd + 2);
			$input->readRe("\\s*");

			if (!$this->parser->parse($input, $expression)) {
				$code = $input->getInput();
				$position = $this->parser->getErrorPosition();
				$expectation = $this->parser->getErrorExpectation();
				$exception = new ParserException($expectation);

				throw new EvaluationException($code, $position, $exception);
			}

			if (!$input->readRe("\\s*:>")) {
				$code = $input->getInput();
				$position = $input->getPosition();
				$message = 'Missing “:>” tag';
				$exception = new Exception($message);

				throw new EvaluationException($code, $position, $exception);
			}

			try {
				$result = $this->evaluator->evaluate($expression);
			} catch (Throwable $throwable) {
				if (
					!($throwable instanceof FileException) &&
					!($throwable instanceof EvaluationException)
				) {
					$code = $input->getInput();
					$position = $input->getPosition();
					$throwable =  new EvaluationException($code, $position, $throwable);
				}

				throw $throwable;
			}

			if ($result !== null) {
				if (!is_string($result)) {
					$code = $input->getInput();
					$position = $input->getPosition();
					$message = 'Expected a string value';
					$exception = new Exception($message);

					throw new EvaluationException($code, $position, $exception);
				}

				$output .= $result;
			}

			$iBegin = $input->getPosition();
		}

		return $output;
	}
}
