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

class Mime
{
	/** @var string */
	private $defaultType;

	/** @var array */
	private $types;

	public function __construct()
	{
		$this->types = [
			'7z' => 'application/x-7z-compressed',
			'aac' => 'audio/aac',
			'abw' => 'application/x-abiword',
			'arc' => 'application/x-freearc',
			'atom' => 'application/atom+xml',
			'avi' => 'video/x-msvideo',
			'azw' => 'application/vnd.amazon.ebook',
			'bmp' => 'image/bmp',
			'bz' => 'application/x-bzip',
			'bz2' => 'application/x-bzip2',
			'csh' => 'application/x-csh',
			'css' => 'text/css',
			'csv' => 'text/csv',
			'divx' => 'video/divx',
			'doc' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'eot' => 'application/vnd.ms-fontobject',
			'epub' => 'application/epub+zip',
			'gif' => 'image/gif',
			'gz' => 'application/x-gzip',
			'htm' => 'text/html',
			'html' => 'text/html',
			'ico' => 'image/x-icon',
			'ics' => 'text/calendar',
			'jar' => 'application/java-archive',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'text/javascript',
			'json' => 'application/json',
			'jsonld' => 'application/ld+json',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mjs' => 'text/javascript',
			'mp3' => 'audio/mpeg',
			'mp4' => 'video/mp4',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'odp' => 'application/vnd.oasis.opendocument.presentation',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt' => 'application/vnd.oasis.opendocument.text',
			'oga' => 'audio/ogg',
			'ogg' => 'application/ogg',
			'ogv' => 'video/ogg',
			'ogx' => 'application/ogg',
			'opus' => 'audio/opus',
			'otf' => 'font/otf',
			'pdf' => 'application/pdf',
			'php' => 'application/x-httpd-php',
			'png' => 'image/png',
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'rar' => 'application/vnd.rar',
			'rtf' => 'application/rtf',
			'sfnt' => 'font/sfnt',
			'svg' => 'image/svg+xml',
			'swf' => 'application/x-shockwave-flash',
			'tar' => 'application/x-tar',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'ts' => 'video/mp2t',
			'ttf' => 'font/ttf',
			'txt' => 'text/plain',
			'wav' => 'audio/x-wav',
			'weba' => 'audio/webm',
			'webm' => 'video/webm',
			'webp' => 'image/webp',
			'wmv' => 'video/x-ms-wmv',
			'woff' => 'font/woff',
			'woff2' => 'font/woff2',
			'xhtml' => 'application/xhtml+xml',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xml' => 'text/xml',
			'zip' => 'application/zip'
		];

		$this->defaultType = 'application/octet-stream';
	}

	public function getType(string $extension)
	{
		if (strlen($extension) === 0) {
			return $this->defaultType;
		}

		$extension = strtolower($extension);

		return $this->types[$extension] ?? $this->defaultType;
	}
}
