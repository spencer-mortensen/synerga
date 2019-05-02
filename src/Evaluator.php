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

class Evaluator
{
	/** @var array */
	private $objects;

	public function __construct(Objects $objects)
	{
		$this->objects = $objects;
	}

	public function evaluate(Call $call)
	{
		$command = $this->getCommand($call);
		$arguments = $this->getArguments($call);
		return $command->run($arguments);
	}

	private function getCommand(Call $call)
	{
		$callName = $call->getName();
		$commandName = $this->getCommandName($callName);
		return $this->objects->get($commandName);
	}

	private function getCommandName($name)
	{
		$words = explode('-', strtolower($name));
		$words = array_map('ucfirst', $words);
		return lcfirst(implode('', $words)) . 'Command';
	}

	private function getArguments(Call $call)
	{
		$arguments = $call->getArguments();
		return new Arguments($this, $arguments);
	}
}
