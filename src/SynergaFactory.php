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
use Synerga\Authenticators\TokenAuthenticator;
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
use Synerga\Commands\MatchCommand;
use Synerga\Commands\MathCommand;
use Synerga\Commands\MathLineCommand;
use Synerga\Commands\MenuCommand;
use Synerga\Commands\NotCommand;
use Synerga\Commands\OrCommand;
use Synerga\Commands\PageCommand;
use Synerga\Commands\PathCommand;
use Synerga\Commands\SetCommand;
use Synerga\Commands\TitleCommand;
use Synerga\Commands\TokenAuthenticateCommand;
use Synerga\Commands\UrlCommand;
use Synerga\Interpreter\Parser;

class SynergaFactory extends Factory
{
	public function getSettings()
	{
		return [
			'command:cookieAuthenticate' => [$this, 'newCookieAuthenticateCommand'],
			'command:date' => [$this, 'newDateCommand'],
			'command:exists' => [$this, 'newExistsCommand'],
			'command:file' => [$this, 'newFileCommand'],
			'command:formAuthenticate' => [$this, 'newFormAuthenticateCommand'],
			'command:get' => [$this, 'newGetCommand'],
			'command:head' => [$this, 'newHeadCommand'],
			'command:html' => [$this, 'newHtmlCommand'],
			'command:http' => [$this, 'newHttpCommand'],
			'command:if' => [$this, 'newIfCommand'],
			'command:include' => [$this, 'newIncludeCommand'],
			'command:integer' => [$this, 'newIntegerCommand'],
			'command:join' => [$this, 'newJoinCommand'],
			'command:match' => [$this, 'newMatchCommand'],
			'command:math' => [$this, 'newMathCommand'],
			'command:math-line' => [$this, 'newMathLineCommand'],
			'command:menu' => [$this, 'newMenuCommand'],
			'command:not' => [$this, 'newNotCommand'],
			'command:or' => [$this, 'newOrCommand'],
			'command:page' => [$this, 'newPageCommand'],
			'command:path' => [$this, 'newPathCommand'],
			'command:set' => [$this, 'newSetCommand'],
			'command:title' => [$this, 'newTitleCommand'],
			'command:tokenAuthenticate' => [$this, 'newTokenAuthenticateCommand'],
			'command:url' => [$this, 'newUrlCommand'],
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
			'tokenAuthenticator' => [$this, 'newTokenAuthenticator'],
			'tokens' => [$this, 'newTokens'],
			'url' => [$this, 'newUrl'],
			'users' => [$this, 'newUsers'],
			'values' => $this,
			'variables' => [$this, 'newVariables']
		];
	}

	public function newCookieAuthenticateCommand()
	{
		$cookieAuthenticator = $this->get('cookieAuthenticator');
		$variables = $this->get('variables');

		return new CookieAuthenticateCommand($cookieAuthenticator, $variables);
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

	public function newDateCommand()
	{
		return new DateCommand();
	}

	public function newEvaluator()
	{
		$values = $this->get('values');

		return new Evaluator($values);
	}

	public function newExistsCommand()
	{
		$data = $this->get('data');

		return new ExistsCommand($data);
	}

	public function newFile()
	{
		$data = $this->get('data');
		$mime = $this->get('mime');

		return new File($data, $mime);
	}

	public function newFileCommand()
	{
		$data = $this->get('data');
		$file = $this->get('file');

		return new FileCommand($data, $file);
	}

	public function newFormAuthenticateCommand()
	{
		$formAuthenticator = $this->get('formAuthenticator');
		$variables = $this->get('variables');

		return new FormAuthenticateCommand($formAuthenticator, $variables);
	}

	public function newFormAuthenticator()
	{
		$users = $this->get('users');
		$sessions = $this->get('sessions');
		$cookies = $this->get('cookies');

		return new FormAuthenticator($users, $sessions, $cookies);
	}

	public function newGetCommand()
	{
		$variables = $this->get('variables');

		return new GetCommand($variables);
	}
	
	public function newHeadCommand()
	{
		$page = $this->get('page');

		return new HeadCommand($page);
	}
	
	public function newHtmlCommand()
	{
		$page = $this->get('page');

		return new HtmlCommand($page);
	}
	
	public function newHttpCommand()
	{
		return new HttpCommand();
	}
	
	public function newIfCommand()
	{
		return new IfCommand();
	}
	
	public function newIncludeCommand()
	{
		$data = $this->get('data');
		$interpreter = $this->get('interpreter');

		return new IncludeCommand($data, $interpreter);
	}
	
	public function newIntegerCommand()
	{
		return new IntegerCommand();
	}

	public function newInterpreter()
	{
		$parser = $this->get('parser');
		$evaluator = $this->get('evaluator');

		return new Interpreter($parser, $evaluator);
	}

	public function newJoinCommand()
	{
		return new JoinCommand();
	}

	public function newMatchCommand()
	{
		return new MatchCommand();
	}
	
	public function newMathCommand()
	{
		$page = $this->get('page');

		return new MathCommand($page);
	}
	
	public function newMathLineCommand()
	{
		$page = $this->get('page');

		return new MathLineCommand($page);
	}
	
	public function newMenuCommand()
	{
		$url = $this->get('url');

		return new MenuCommand($url);
	}

	public function newMime()
	{
		$data = $this->get('data');

		return new Mime($data);
	}
	
	public function newNotCommand()
	{
		return new NotCommand();
	}
	
	public function newOrCommand()
	{
		return new OrCommand();
	}

	public function newPage()
	{
		return new Page();
	}
	
	public function newPageCommand()
	{
		$data = $this->get('data');
		$interpreter = $this->get('interpreter');
		$page = $this->get('page');

		return new PageCommand($data, $interpreter, $page);
	}

	public function newParser()
	{
		return new Parser();
	}
	
	public function newPathCommand()
	{
		$url = $this->get('url');

		return new PathCommand($url);
	}

	public function newSessions()
	{
		$data = $this->get('data');

		return new Sessions($data);
	}

	public function newSetCommand()
	{
		$variables = $this->get('variables');

		return new SetCommand($variables);
	}
	
	public function newTitleCommand()
	{
		$page = $this->get('page');

		return new TitleCommand($page);
	}

	public function newTokenAuthenticateCommand()
	{
		$tokenAuthenticator = $this->get('tokenAuthenticator');
		$variables = $this->get('variables');

		return new TokenAuthenticateCommand($tokenAuthenticator, $variables);
	}

	public function newTokenAuthenticator()
	{
		$tokens = $this->get('tokens');
		$sessions = $this->get('sessions');
		$cookies = $this->get('cookies');
		$url = $this->get('url');

		return new TokenAuthenticator($tokens, $sessions, $cookies, $url);
	}

	public function newTokens()
	{
		$data = $this->get('data');

		return new Tokens($data);
	}

	public function newUrl()
	{
		$settings = $this->get('settings:url');

		$urlBase = $settings['base'];
		$urlPath = $settings['path'];

		return new Url($urlBase, $urlPath);
	}

	public function newUrlCommand()
	{
		$url = $this->get('url');

		return new UrlCommand($url);
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
		return new Variables();
	}
}
