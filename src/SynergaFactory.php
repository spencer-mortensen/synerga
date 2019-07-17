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

use Synerga\Authenticators\CookieAuthenticator;
use Synerga\Authenticators\FormAuthenticator;
use Synerga\Commands\ControllerCommand;
use Synerga\Commands\CookieAuthenticateCommand;
use Synerga\Commands\DateCommand;
use Synerga\Commands\ExistsCommand;
use Synerga\Commands\FileCommand;
use Synerga\Commands\FormAuthenticateCommand;
use Synerga\Commands\GetCommand;
use Synerga\Commands\HeadCommand;
use Synerga\Commands\HtmlCommand;
use Synerga\Commands\HttpCommand;
use Synerga\Commands\IfCommand;
use Synerga\Commands\IncludeCommand;
use Synerga\Commands\IntegerCommand;
use Synerga\Commands\JoinCommand;
use Synerga\Commands\MathCommand;
use Synerga\Commands\MathLineCommand;
use Synerga\Commands\MenuCommand;
use Synerga\Commands\NotCommand;
use Synerga\Commands\OrCommand;
use Synerga\Commands\PageCommand;
use Synerga\Commands\PathCommand;
use Synerga\Commands\SetCommand;
use Synerga\Commands\TitleCommand;
use Synerga\Commands\UrlCommand;
use Synerga\Interpreter\Parser;

class SynergaFactory extends Factory
{
	public function getSettings()
	{
		return [
			'command:controller' => [$this, 'newCommandController'],
			'command:cookieAuthenticate' => [$this, 'newCommandCookieAuthenticate'],
			'command:date' => [$this, 'newCommandDate'],
			'command:exists' => [$this, 'newCommandExists'],
			'command:file' => [$this, 'newCommandFile'],
			'command:formAuthenticate' => [$this, 'newCommandFormAuthenticate'],
			'command:get' => [$this, 'newCommandGet'],
			'command:head' => [$this, 'newCommandHead'],
			'command:html' => [$this, 'newCommandHtml'],
			'command:http' => [$this, 'newCommandHttp'],
			'command:if' => [$this, 'newCommandIf'],
			'command:include' => [$this, 'newCommandInclude'],
			'command:integer' => [$this, 'newCommandInteger'],
			'command:join' => [$this, 'newCommandJoin'],
			'command:math' => [$this, 'newCommandMath'],
			'command:math-line' => [$this, 'newCommandMathLine'],
			'command:menu' => [$this, 'newCommandMenu'],
			'command:not' => [$this, 'newCommandNot'],
			'command:or' => [$this, 'newCommandOr'],
			'command:page' => [$this, 'newCommandPage'],
			'command:path' => [$this, 'newCommandPath'],
			'command:set' => [$this, 'newCommandSet'],
			'command:title' => [$this, 'newCommandTitle'],
			'command:url' => [$this, 'newCommandUrl'],
			'cookieAuthenticator' => [$this, 'newCookieAuthenticator'],
			'cookies' => [$this, 'newCookies'],
			'cookies:options' => null,
			'data' => [$this, 'newData'],
			'evaluator' => [$this, 'newEvaluator'],
			'file' => [$this, 'newFile'],
			'formAuthenticator' => [$this, 'newFormAuthenticator'],
			'interpreter' => [$this, 'newInterpreter'],
			'mime' => [$this, 'newMime'],
			'page' => [$this, 'newPage'],
			'parser' => [$this, 'newParser'],
			'sessions' => [$this, 'newSessions'],
			'url' => [$this, 'newUrl'],
			'users' => [$this, 'newUsers'],
			'values' => $this,
			'variables' => [$this, 'newVariables']
		];
	}

	public function newCommandController()
	{
		$url = $this->get('url');
		$evaluator = $this->get('evaluator');

		return new ControllerCommand($url, $evaluator);
	}

	public function newCommandCookieAuthenticate()
	{
		$cookieAuthenticator = $this->get('cookieAuthenticator');
		$variables = $this->get('variables');

		return new CookieAuthenticateCommand($cookieAuthenticator, $variables);
	}

	public function newCommandDate()
	{
		return new DateCommand();
	}

	public function newCommandExists()
	{
		$data = $this->get('data');

		return new ExistsCommand($data);
	}

	public function newCommandFile()
	{
		$file = $this->get('file');

		return new FileCommand($file);
	}

	public function newCommandFormAuthenticate()
	{
		$formAuthenticator = $this->get('formAuthenticator');
		$variables = $this->get('variables');

		return new FormAuthenticateCommand($formAuthenticator, $variables);
	}

	public function newCommandGet()
	{
		$variables = $this->get('variables');

		return new GetCommand($variables);
	}
	
	public function newCommandHead()
	{
		$page = $this->get('page');

		return new HeadCommand($page);
	}
	
	public function newCommandHtml()
	{
		$page = $this->get('page');

		return new HtmlCommand($page);
	}
	
	public function newCommandHttp()
	{
		return new HttpCommand();
	}
	
	public function newCommandIf()
	{
		return new IfCommand();
	}
	
	public function newCommandInclude()
	{
		$data = $this->get('data');
		$interpreter = $this->get('interpreter');

		return new IncludeCommand($data, $interpreter);
	}
	
	public function newCommandInteger()
	{
		return new IntegerCommand();
	}
	
	public function newCommandJoin()
	{
		return new JoinCommand();
	}
	
	public function newCommandMath()
	{
		$page = $this->get('page');

		return new MathCommand($page);
	}
	
	public function newCommandMathLine()
	{
		$page = $this->get('page');

		return new MathLineCommand($page);
	}
	
	public function newCommandMenu()
	{
		$url = $this->get('url');

		return new MenuCommand($url);
	}
	
	public function newCommandNot()
	{
		return new NotCommand();
	}
	
	public function newCommandOr()
	{
		return new OrCommand();
	}
	
	public function newCommandPage()
	{
		$data = $this->get('data');
		$interpreter = $this->get('interpreter');
		$page = $this->get('page');

		return new PageCommand($data, $interpreter, $page);
	}
	
	public function newCommandPath()
	{
		$url = $this->get('url');

		return new PathCommand($url);
	}
	
	public function newCommandSet()
	{
		$variables = $this->get('variables');

		return new SetCommand($variables);
	}
	
	public function newCommandTitle()
	{
		$page = $this->get('page');

		return new TitleCommand($page);
	}
	
	public function newCommandUrl()
	{
		$url = $this->get('url');

		return new UrlCommand($url);
	}
	
	public function newCookieAuthenticator()
	{
		$sessions = $this->get('sessions');
		$cookies = $this->get('cookies');

		return new CookieAuthenticator($sessions, $cookies);
	}
	
	public function newCookies()
	{
		$cookiesOptions = $this->get('cookies:options');

		return new Cookies($cookiesOptions);
	}
	
	public function newData()
	{
		$dataPath = $this->get('settings:data:path');

		return new Data($dataPath);
	}
	
	public function newEvaluator()
	{
		$values = $this->get('values');

		return new Evaluator($values);
	}
	
	public function newFile()
	{
		$data = $this->get('data');
		$mime = $this->get('mime');

		return new File($data, $mime);
	}
	
	public function newFormAuthenticator()
	{
		$users = $this->get('users');
		$sessions = $this->get('sessions');
		$cookies = $this->get('cookies');

		return new FormAuthenticator($users, $sessions, $cookies);
	}
	
	public function newInterpreter()
	{
		$parser = $this->get('parser');
		$evaluator = $this->get('evaluator');

		return new Interpreter($parser, $evaluator);
	}
	
	public function newMime()
	{
		$data = $this->get('data');

		return new Mime($data);
	}
	
	public function newPage()
	{
		return new Page();
	}
	
	public function newParser()
	{
		return new Parser();
	}
	
	public function newSessions()
	{
		$data = $this->get('data');

		return new Sessions($data);
	}
	
	public function newUrl()
	{
		$settings = $this->get('settings:url');

		$urlBase = $settings['base'];
		$urlPath = $settings['path'];

		return new Url($urlBase, $urlPath);
	}
	
	public function newUsers()
	{
		$data = $this->get('data');

		return new Users($data);
	}

	public function newValues()
	{
		return $this;
	}

	public function newVariables()
	{
		return new variables();
	}
}
