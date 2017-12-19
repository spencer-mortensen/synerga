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

class Synerga
{
	/** @var array */
	private $services;

	// TODO: move this to the user configuration:
	private function command($name, array $arguments = array())
	{
		switch ($name) {
			case 'link':
				$link = $this->get('link');
				return $link->getHtml($arguments[0], $arguments[1]);

			case 'include':
				$data = $this->get('data');
				$synerga = $this->get('synerga');
				$contents = $data->read($arguments[0]);
				return $synerga->run($contents);

			case 'path':
				$url = $this->get('url');
				return $url->getPath();

			case 'router':
				$router = $this->get('router');
				return $router->run();

			default:
				return null;
		}
	}

	// TODO: move this to user configuration:
	private function service($name)
	{
		switch ($name) {
			case 'data':
				return new Data($GLOBALS['data']);

			case 'link':
				$data = $this->get('data');
				$url = $this->get('url');
				return new Link($data, $url);

			case 'file':
				$data = $this->get('data');
				$mime = $this->get('mime');
				return new File($data, $mime);

			case 'mime':
				$data = $this->get('data');
				return new Mime($data);

			case 'page':
				$synerga = $this->get('synerga');
				$data = $this->get('data');
				return new Page($synerga, $data);

			case 'router':
				$data = $this->get('data');
				$url = $this->get('url');
				$page = $this->get('page');
				$file = $this->get('file');
				return new Router($data, $url, $page, $file);

			case 'url':
				return new Url($_SERVER['SYNERGA_BASE'], $_SERVER['SYNERGA_PATH']);
		}
	}

	public function __construct()
	{
		$this->services = array(
			'synerga' => $this
		);
	}

	public function run($input)
	{
		if (!is_string($input)) {
			return null;
		}

		$parser = new Parser($input);

		$output = '';

		while ($parser->getCommand($text, $name, $arguments)) {
			foreach ($arguments as &$argument) {
				if (is_string($argument)) {
					$argument = $this->run($argument);
				}
			}

			$output .= $text . $this->command($name, $arguments);
		}

		$output .= $parser->getText();

		return $output;
	}

	private static function getCommand($input, &$name, &$arguments)
	{
		$input = trim($input);

		if (strlen($input) === 0) {
			return false;
		}

		$commandParts = explode(' ', $input, 2);

		$name = $commandParts[0];
		$arguments = self::getCommandArguments($commandParts[1]);

		return true;
	}

	private static function getCommandArguments(&$input)
	{
		if (!is_string($input)) {
			return array();
		}

		return array(
			json_decode($input, true)
		);
	}

	private function get($name)
	{
		$service = &$this->services[$name];

		if ($service === null) {
			$service = $this->service($name);
		}

		return $service;
	}
}
