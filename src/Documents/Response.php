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

namespace Synerga\Documents;

use Exception;

class Response
{
	private $code;
	private $headers;
	private $content;

	public function __construct(string $code, array $headers = [], string $content = null)
	{
		$this->code = $code;
		$this->headers = $headers;
		$this->content = $content;
	}

	public function send()
	{
		header("HTTP/1.1 {$this->code}");

		if ($this->content !== null) {
			$this->headers['Content-Length'] = (string)strlen($this->content);
		}

		foreach ($this->headers as $key => $value) {
			header("{$key}: {$value}");
		}

		if ($this->content !== null) {
			echo $this->content;
		}
	}
}
