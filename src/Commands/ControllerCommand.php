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
use Synerga\Call;
use Synerga\Evaluator;
use Synerga\Url;

class ControllerCommand implements Command
{
	/** @var Url */
	private $url;

	public function __construct(Url $url, Evaluator $evaluator)
	{
		$this->url = $url;
		$this->evaluator = $evaluator;
	}

	public function run(Arguments $arguments)
	{
		$map = $arguments->getArray(0);
		$path = $this->url->getPath();

		if (!$this->getAction($map, $path, $action)) {
			// TODO: improve exceptions
			throw new Exception('No action found');
		}

		// TODO: evaluate calls in arrays?
		if ($action instanceof Call) {
			$action = $this->evaluator->evaluate($action);
		}

		return $action;
	}

	private function getAction(array $map, string $path, &$action)
	{
		foreach ($map as $expression => $action) {
			if (preg_match("\x03{$expression}\x03XDs", $path) === 1) {
				return true;
			}
		}

		return false;
	}
}
