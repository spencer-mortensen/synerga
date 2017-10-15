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

class File
{
	/** @var Data */
	private $data;

	/** @var Mime */
	private $mime;

	public function __construct(Data $data, Mime $mime)
	{
		$this->data = $data;
		$this->mime = $mime;
	}

	public function send($path)
	{
		$mtime = $this->data->mtime($path);

		if ($mtime === null) {
			header('HTTP/1.0 404 Not Found');
			exit(0);
		}

		$eTag = self::getETag($mtime);
		header("ETag: \"{$eTag}\"");

		if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
		{
			// For GET or HEAD methods:
			if ($eTag === trim($_SERVER['HTTP_IF_NONE_MATCH'], ' "'))
			{
				// For GET or HEAD methods:
				header('HTTP/1.1 304 Not Modified');
				exit(0);
			}

			// For all other methods:
			// 412 Precondition Failed
		}

		$mimeType = $this->mime->getType($path);
		header("Content-Type: {$mimeType}");

		$content = $this->data->read($path);
		$contentLength = strlen($content);
		header("Content-Length: {$contentLength}");

		$this->data->send($path);
		exit(0);
	}

	private static function getETag($mtime)
	{
		return base_convert($mtime, 10, 36);
	}
}
