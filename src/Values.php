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

use Exception;

class Values
{
	private $settings;
	private $values;

	public function __construct(array $settings)
	{
		$this->settings = $settings;
		$this->values = [];
	}

	public function set($name, $value)
	{
		$this->values[$name] = $value;
	}

	public function get($name)
	{
		if (!array_key_exists($name, $this->values)) {
			$this->values[$name] = $this->instantiate($name);
		}

		return $this->values[$name];
	}

	private function instantiate($name)
	{
		if (!array_key_exists($name, $this->settings)) {
			throw new Exception("Missing setting: {$name}");
		}

		$setting = $this->settings[$name];

		if ($setting instanceof Instance) {
			return $this->getInstance($setting);
		}

		return $setting;
	}

	private function getInstance(Instance $instance)
	{
		$class = $instance->getClass();
		$parameters = $instance->getParameters();
		$arguments = $this->getArguments($parameters);

		return new $class(...$arguments);
	}

	private function getArguments(array $parameters)
	{
		$arguments = [];

		foreach ($parameters as $name) {
			$arguments[] = $this->get($name);
		}

		return $arguments;
	}
}
