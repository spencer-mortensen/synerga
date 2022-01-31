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
use Synerga\Data;
use Synerga\Html;
use Synerga\Interpreter\Interpreter;
use Synerga\Url;

class LinkCommand implements Command
{
	/** @var Data */
	private $data;

	/** @var Interpreter */
	private $interpreter;

	/** @var Url */
	private $url;

	/** @var Html */
	private $html;

	public function __construct(Data $data, Interpreter $interpreter, Url $url, Html $html)
	{
		$this->data = $data;
		$this->interpreter = $interpreter;
		$this->url = $url;
		$this->html = $html;
	}

	public function run(Arguments $arguments)
	{
		$path = $arguments->getString(0);
		$title = $this->getTitle($path);

		return $this->getAHtml($path, $title);
	}

	private function getTitle(string $path): string
	{
		$this->getPathText("{$path}.page/.title/", $text)
		|| $this->getPathText("{$path}.title/", $text);

		if ($text === null) {
			return '';
		}

		return $this->interpreter->interpret($text);
	}

	private function getPathText(string $key, &$text): bool
	{
		$text = $this->data->read($key);

		return ($text !== null);
	}

	private function getAHtml(string $path, string $text): string
	{
		$url = $this->url->getUrl($path);
		$urlHtml = $this->html->encode($url);
		$textHtml = $this->html->encode($text);

		return "<a href=\"{$urlHtml}\">{$textHtml}</a>";
	}
}
