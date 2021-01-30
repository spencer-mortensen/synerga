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

class Html
{
	/** @var int */
	private $flags;

	/** @var string */
	private $encoding;

	public function __construct(string $format)
	{
		$this->flags = self::getFormatFlag($format);
		$this->encoding = 'UTF-8';
	}

	public function encode(string $text): string
	{
		return htmlspecialchars($text, $this->flags | ENT_QUOTES, $this->encoding);
	}

	private static function getFormatFlag(string $format): int
	{
		switch ($format) {
			case 'HTML5':
				return ENT_HTML5;

			case 'HTML4':
				return ENT_HTML401;

			case 'XHTML':
				return ENT_XHTML;

			case 'XML1':
				return ENT_XML1;

			default:
				throw new Exception("Unrecognized doctype: {$format}");
		}
	}
}
