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
use Synerga\Data;
use Synerga\Mime;

class File
{
	/** @var Data */
	private $data;

	/** @var Mime */
	private $mime;

	/** @var mixed */
	private $cache;

	public function __construct(Data $data, Mime $mime, $cache)
	{
		$this->data = $data;
		$this->mime = $mime;
		$this->cache = $cache;
	}

	public function send(string $path)
	{
		$this->addCacheControlHeader();

		$clientETag = $this->getClientETag();
		$serverETag = $this->newServerETag($path);

		$this->addETagHeader($serverETag);

		$this->sendUnmodifiedHeader($clientETag, $serverETag) ||
		$this->sendFile($path);
	}

	private function getClientETag()
	{
		$eTag = $_SERVER['HTTP_IF_NONE_MATCH'] ?? null;

		if ($eTag === null) {
			return null;
		}

		$eTag = trim($eTag, '"');
		$i = strpos($eTag, '-');

		if ($i !== false) {
			$eTag = substr($eTag, 0, $i);
		}

		return $eTag;
	}

	private function newServerETag(string $path)
	{
		$mtime = $this->data->mtime($path);

		if ($mtime === null) {
			throw new Exception('Unable to read the file modification time.');
		}

		return base_convert($mtime, 10, 36);
	}

	private function addETagHeader(string $eTag)
	{
		header("ETag: \"{$eTag}\"");
	}

	private function addCacheControlHeader()
	{
		if ($this->cache === null) {
			return;
		}

		if ($this->cache) {
			$directives = 'public, max-age=31536000, immutable';
		} else {
			$directives = 'no-store, max-age=0';
		}

		header("Cache-Control: {$directives}");
	}

	private function sendUnmodifiedHeader($clientETag, string $serverETag)
	{
		if ($clientETag !== $serverETag) {
			return false;
		}

		$method = $_SERVER['REQUEST_METHOD'];

		if (!(($method === 'GET') || ($method === 'HEAD'))) {
			return false;
		}


		header('HTTP/1.1 304 Not Modified');
		return true;
	}

	private function sendFile(string $path)
	{
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		$mimeType = $this->mime->getType($extension);
		$sizeBytes = $this->data->getSizeBytes($path);

		header("HTTP/1.1 200 OK");
		header("Content-Type: {$mimeType}");
		header("Content-Length: {$sizeBytes}");
		$this->data->send($path);

		return true;
	}
}
