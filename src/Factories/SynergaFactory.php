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

namespace Synerga\Factories;

use Synerga\Authenticators\CookieAuthenticator;
use Synerga\Authenticators\FormAuthenticator;
use Synerga\Commands\ControllerCommand;
use Synerga\Commands\CookieAuthenticateCommand;
use Synerga\Commands\CssCommand;
use Synerga\Commands\DateCommand;
use Synerga\Commands\ExistsCommand;
use Synerga\Commands\FileCommand;
use Synerga\Commands\FormAuthenticateCommand;
use Synerga\Commands\GetCommand;
use Synerga\Commands\HttpCommand;
use Synerga\Commands\IfCommand;
use Synerga\Commands\IncludeCommand;
use Synerga\Commands\IntegerCommand;
use Synerga\Commands\JoinCommand;
use Synerga\Commands\OrCommand;
use Synerga\Commands\PathCommand;
use Synerga\Commands\SetCommand;
use Synerga\Commands\TodoCommand;
use Synerga\Commands\UrlCommand;
use Synerga\Cookies;
use Synerga\Data;
use Synerga\Evaluator;
use Synerga\File;
use Synerga\Interpreter;
use Synerga\Mime;
use Synerga\Objects;
use Synerga\PageData;
use Synerga\Parser;
use Synerga\Sessions;
use Synerga\Url;
use Synerga\Users;
use Synerga\Variables;

class SynergaFactory implements Factory
{
	public function newControllerCommand(Objects $objects)
	{
		$url = $objects->get('url');
		$evaluator = $objects->get('evaluator');

		return new ControllerCommand($url, $evaluator);
	}

	public function newCookieAuthenticator(Objects $objects)
	{
		$sessions = $objects->get('sessions');
		$cookies = $objects->get('cookies');

		return new CookieAuthenticator($sessions, $cookies);
	}

	public function newCookieAuthenticateCommand(Objects $objects)
	{
		$cookieAuthenticator = $objects->get('cookieAuthenticator');
		$variables = $objects->get('variables');

		return new CookieAuthenticateCommand($cookieAuthenticator, $variables);
	}

	public function newCookies(Objects $objects)
	{
		// TODO: configure or override?
		$options = null;

		return new Cookies($options);
	}

	public function newCssCommand(Objects $objects)
	{
		$head = $options->getHead();

		return new CssCommand($head);
	}

	public function newData()
	{
		return new Data($GLOBALS['data']);
	}

	public function newDateCommand()
	{
		return new DateCommand();
	}

	public function newExistsCommand(Objects $objects)
	{
		$data = $objects->get('data');

		return new ExistsCommand($data);
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

	public function newFileCommand(Objects $objects)
	{
		$file = $objects->get('file');

		return new FileCommand($file);
	}

	public function newFormAuthenticator(Objects $objects)
	{
		$users = $objects->get('users');
		$sessions = $objects->get('sessions');
		$cookies = $objects->get('cookies');

		return new FormAuthenticator($users, $sessions, $cookies);
	}

	public function newFormAuthenticateCommand(Objects $objects)
	{
		$formAuthenticator = $objects->get('formAuthenticator');
		$variables = $objects->get('variables');

		return new FormAuthenticateCommand($formAuthenticator, $variables);
	}

	public function newGetCommand(Objects $objects)
	{
		$variables = $objects->get('variables');

		return new GetCommand($variables);
	}

	public function newHttpCommand()
	{
		return new HttpCommand();
	}

	public function newIfCommand()
	{
		return new IfCommand();
	}

	public function newIncludeCommand(Objects $objects)
	{
		$data = $objects->get('data');
		$interpreter = $objects->get('interpreter');

		return new IncludeCommand($data, $interpreter);
	}

	public function newIntegerCommand()
	{
		return new IntegerCommand();
	}

	public function newJoinCommand()
	{
		return new JoinCommand();
	}

	public function newInterpreter(Objects $objects)
	{
		$parser = $objects->get('parser');
		$evaluator = $objects->get('evaluator');

		return new Interpreter($parser, $evaluator);
	}

	public function newMime(Objects $objects)
	{
		$data = $objects->get('data');

		return new Mime($data);
	}

	public function newOrCommand()
	{
		return new OrCommand();
	}

	public function newPageData(Objects $objects)
	{
		$interpreter = $objects->get('interpreter');
		$data = $objects->get('data');

		return new PageData($interpreter, $data);
	}

	public function newParser()
	{
		return new Parser();
	}

	public function newPathCommand(Objects $objects)
	{
		$url = $objects->get('url');

		return new PathCommand($url);
	}

	public function newSessions(Objects $objects)
	{
		$data = $objects->get('data');

		return new Sessions($data);
	}

	public function newSetCommand(Objects $objects)
	{
		$variables = $objects->get('variables');

		return new SetCommand($variables);
	}

	public function newUrl()
	{
		$baseUrl = $_SERVER['SYNERGA_BASE'];
		$path = $_SERVER['SYNERGA_PATH'];

		return new Url($baseUrl, $path);
	}

	public function newUrlCommand(Objects $objects)
	{
		$url = $objects->get('url');

		return new UrlCommand($url);
	}

	public function newUsers(Objects $objects)
	{
		$data = $objects->get('data');

		return new Users($data);
	}

	public function newVariables()
	{
		return new Variables();
	}
}
