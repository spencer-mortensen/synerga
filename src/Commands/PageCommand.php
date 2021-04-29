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

namespace Synerga\Commands;

use Exception;
use Synerga\Arguments;
use Synerga\Data;
use Synerga\ErrorHandling\Exceptions\FileException;
use Synerga\ErrorHandling\Exceptions\EvaluationException;
use Synerga\Interpreter\Interpreter;
use Synerga\Page;
use Synerga\StringInput;
use Throwable;

class PageCommand implements Command
{
	private $data;
	private $interpreter;
	private $page;

	public function __construct(Data $data, Interpreter $interpreter, Page $page)
	{
		$this->data = $data;
		$this->interpreter = $interpreter;
		$this->page = $page;
	}

	public function run(Arguments $arguments)
	{
		for ($i = 0, $n = $arguments->count(); $i < $n; ++$i) {
			$path = $arguments->getString($i);
			$this->setPage($path);
		}

		return $this->page->getBody();
	}

	private function setPage(string $path)
	{
		$this->setTitle($path);
		$this->addHead($path);
		$this->setBody($path);
	}

	private function setTitle(string $path)
	{
		$title = $this->getString("{$path}.title/");

		if ($title === null) {
			return;
		}

		$this->page->setTitle($title);
	}

	private function getString(string $path)
	{
		$contents = $this->data->read($path);

		if ($contents === null) {
			return null;
		}

		try {
			$value = $this->interpreter->interpret($contents);
		} catch (EvaluationException $exception) {
			throw new FileException($path, $exception);
		}

		if (strlen($value) === 0) {
			return null;
		}

		return $value;
	}

	private function addHead(string $path)
	{
		$html = $this->getString("{$path}.head/");

		if ($html === null) {
			return;
		}

		if (!$this->getElements($html, $head)) {
			throw new Exception('Unknown head elements');
		}

		$this->page->addHead(array_reverse($head));
	}

	private function getElements(string $html, &$head): bool
	{
		$head = [];

		$input = new StringInput($html);
		$input->readRe('\\s*');

		while (
			$input->readRe('<([a-z]+)\\b[^>]*>(?:[^<]*</\\1>)?', $match) &&
			$input->readRe('\\s*')
		) {
			$head[] = $match[0];
		}

		return $input->readEnd();
	}

	private function setBody(string $path)
	{
		$html = $this->getString("{$path}.body/");

		if ($html === null) {
			return;
		}

		$this->page->setBody($html);
	}
}
