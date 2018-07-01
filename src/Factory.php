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

use Synerga\Commands\DateCommand;
use Synerga\Commands\IfCommand;
use Synerga\Commands\IncludeCommand;
use Synerga\Commands\PathCommand;
use Synerga\Commands\RouterCommand;
use Synerga\Commands\UrlCommand;

class Factory
{
	public function newCommandDate(Objects $objects)
	{
		$evaluator = $objects->get('evaluator');

		return new DateCommand($evaluator);
	}

	public function newCommandIf(Objects $objects)
	{
		$evaluator = $objects->get('evaluator');

		return new IfCommand($evaluator);
	}

	public function newCommandInclude(Objects $objects)
	{
		$scanner = $objects->get('scanner');
		$data = $objects->get('data');

		return new IncludeCommand($scanner, $data);
	}

	public function newCommandPath(Objects $objects)
	{
		$url = $objects->get('url');

		return new PathCommand($url);
	}

	public function newCommandRouter(Objects $objects)
	{
		$url = $objects->get('url');
		$data = $objects->get('data');
		$page = $objects->get('page');
		$file = $objects->get('file');

		return new RouterCommand($url, $data, $page, $file);
	}

	public function newCommandUrl(Objects $objects)
	{
		$scanner = $objects->get('scanner');
		$url = $objects->get('url');

		return new UrlCommand($scanner, $url);
	}

	public function newData()
	{
		return new Data($GLOBALS['data']);
	}

	public function newEvaluator(Objects $objects)
	{
		return new Evaluator($objects);
	}

	public function newFile(Objects $objects)
	{
		$data = $objects->get('data');
		$mime = $objects->get('mime');

		return new File($data, $mime);
	}

	public function newMime(Objects $objects)
	{
		$data = $objects->get('data');

		return new Mime($data);
	}

	public function newPage(Objects $objects)
	{
		$scanner = $objects->get('scanner');
		$data = $objects->get('data');

		return new Page($scanner, $data);
	}

	public function newParser()
	{
		return new Parser();
	}

	public function newScanner(Objects $objects)
	{
		$parser = $objects->get('parser');
		$evaluator = $objects->get('evaluator');

		return new Scanner($parser, $evaluator);
	}

	public function newUrl()
	{
		$baseUrl = $_SERVER['SYNERGA_BASE'];
		$path = $_SERVER['SYNERGA_PATH'];

		return new Url($baseUrl, $path);
	}
}
