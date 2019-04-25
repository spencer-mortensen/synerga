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

	/** @var Evaluator */
	private $evaluator;

	public function __construct(Parser $parser, Evaluator $evaluator)
	{
		$this->parser = $parser;
		$this->evaluator = $evaluator;
	}

	public function scan(string $text): string
	{
		$input = new StringInput($text);

		$this->getOutput($input, $output);

		return $output;
	}

	public function getOutput(StringInput $input, &$output): bool
	{
		$output = '';

		while (
			$this->getCommand($input, $text) ||
			$this->getText($input, $text)
		) {
			$output .= $text;
		}

		return true;
	}

	private function getText(StringInput $input, &$output): bool
	{
		$text = $input->getInput();
		$iBegin = $input->getPosition();

		if (strlen($text) <= $iBegin) {
			return false;
		}

		$iEnd = strpos($text, '<:', $iBegin);

		if ($iBegin === $iEnd) {
			return false;
		}

		if (!is_int($iEnd)) {
			$iEnd = strlen($text);
		}

		$output = substr($text, $iBegin, $iEnd - $iBegin);
		$input->setPosition($iEnd);
		return true;
	}

	private function getCommand(StringInput $input, &$output): bool
	{
		if ($this->parser->parse($input, $command)) {
			$output = $this->evaluator->run($command);
			return true;
		}

		return false;
	}
}
