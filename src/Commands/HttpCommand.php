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
use Synerga\Documents\Response;

class HttpCommand implements Command
{
	public function run(Arguments $arguments)
	{
		$code = $arguments->getInteger(0);
		$content = $arguments->getString(1);

		if ($code === 200) {
			$this->send('200 OK', $content);
		} elseif ($code === 404) {
			$this->send('400 Not Found', $content);
		} else {
			throw new Exception("Unknown code {$code}");
		}

		exit(0);
	}

	private function send(string $code, string $content = null)
	{
		$headers = [];
		$response = new Response($code, $headers, $content);
		$response->send();
	}
}
