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

use SpencerMortensen\Exceptions\ErrorHandling;
use SpencerMortensen\Logging\FileLogger;
use Synerga\Authenticators\CookieAuthenticator;
use Synerga\Authenticators\FormAuthenticator;
use Synerga\Authenticators\TokenAuthenticator;
use Synerga\Commands\AndCommand;
use Synerga\Commands\BodyCommand;
use Synerga\Commands\CookieAuthenticateCommand;
use Synerga\Commands\DateCommand;
use Synerga\Commands\EqualCommand;
use Synerga\Commands\ExistsCommand;
use Synerga\Commands\FileCommand;
use Synerga\Commands\FormAuthenticateCommand;
use Synerga\Commands\GetCommand;
use Synerga\Commands\HeadCommand;
use Synerga\Commands\HostCommand;
use Synerga\Commands\HtmlCommand;
use Synerga\Commands\Html5NodeCommand;
use Synerga\Commands\HttpCommand;
use Synerga\Commands\IfCommand;
use Synerga\Commands\IncludeCommand;
use Synerga\Commands\IntegerCommand;
use Synerga\Commands\JoinCommand;
use Synerga\Commands\MatchCommand;
use Synerga\Commands\MathCommand;
use Synerga\Commands\MathLineCommand;
use Synerga\Commands\MenuCommand;
use Synerga\Commands\MenuUpCommand;
use Synerga\Commands\NotCommand;
use Synerga\Commands\OrCommand;
use Synerga\Commands\PageCommand;
use Synerga\Commands\PathCommand;
use Synerga\Commands\ReadCommand;
use Synerga\Commands\RedirectCommand;
use Synerga\Commands\SchemeCommand;
use Synerga\Commands\SetCommand;
use Synerga\Commands\SiteCommand;
use Synerga\Commands\StringCommand;
use Synerga\Commands\TitleCommand;
use Synerga\Commands\TokenAuthenticateCommand;
use Synerga\Commands\UrlCommand;
use Synerga\ErrorHandling\ErrorHandler;
use Synerga\Interpreter\Interpreter;
use Synerga\Interpreter\Parser;

class SynergaFactory extends Factory
{
	public function newAndCommand(): AndCommand
	{
		return new AndCommand();
	}

	public function newCookieAuthenticateCommand(): CookieAuthenticateCommand
	{
		return new CookieAuthenticateCommand($this->cookieAuthenticator, $this->variables);
	}

	public function newCookieAuthenticator(): CookieAuthenticator
	{
		return new CookieAuthenticator($this->sessions, $this->cookies);
	}

	public function newCookies(): Cookies
	{
		$cookiesOptions = $this->settings['cookies']['options'] ?? null;

		return new Cookies($cookiesOptions);
	}

	public function newData(): Data
	{
		$dataPath = $this->settings['data'];

		return new Data($dataPath);
	}

	public function newDateCommand(): DateCommand
	{
		return new DateCommand();
	}

	public function newErrorHandling(): ErrorHandling
	{
		$errors = $this->settings['errors'];
		$display = $errors['display'];
		$level = $errors['level'];
		$logPath = $errors['log'];

		$logger = new FileLogger($logPath);
		$handler = new ErrorHandler($logger, $display, $this->html);
		return new ErrorHandling($handler, $level);
	}

	public function newEvaluator(): Evaluator
	{
		return new Evaluator($this);
	}

	public function newEqualCommand(): EqualCommand
	{
		return new EqualCommand();
	}

	public function newExistsCommand(): ExistsCommand
	{
		return new ExistsCommand($this->data);
	}

	public function newFile(): File
	{
		return new File($this->data, $this->mime);
	}

	public function newFileCommand(): FileCommand
	{
		return new FileCommand($this->data, $this->file);
	}

	public function newFormAuthenticateCommand(): FormAuthenticateCommand
	{
		return new FormAuthenticateCommand($this->formAuthenticator, $this->variables);
	}

	public function newFormAuthenticator(): FormAuthenticator
	{
		return new FormAuthenticator($this->users, $this->sessions, $this->cookies);
	}

	public function newGetCommand(): GetCommand
	{
		return new GetCommand($this->variables);
	}
	
	public function newHeadCommand(): HeadCommand
	{
		return new HeadCommand($this->page, $this->html);
	}

	public function newHostCommand(): HostCommand
	{
		$host = $this->settings['url']['host'];

		return new HostCommand($host);
	}

	public function newBodyCommand(): BodyCommand
	{
		return new BodyCommand($this->page);
	}

	public function newHtml(): Html
	{
		// TODO: get page format from the configuration file
		$format = 'HTML5';

		return new Html($format);
	}

	public function newHtmlCommand(): HtmlCommand
	{
		return new HtmlCommand($this->html);
	}

	public function newHtml5NodeCommand(): Html5NodeCommand
	{
		return new Html5NodeCommand($this->html);
	}
	
	public function newHttpCommand(): HttpCommand
	{
		return new HttpCommand();
	}
	
	public function newIfCommand(): IfCommand
	{
		return new IfCommand();
	}
	
	public function newIncludeCommand(): IncludeCommand
	{
		return new IncludeCommand($this->data, $this->interpreter);
	}
	
	public function newIntegerCommand(): IntegerCommand
	{
		return new IntegerCommand();
	}

	public function newInterpreter(): Interpreter
	{
		return new Interpreter($this->parser, $this->evaluator);
	}

	public function newJoinCommand(): JoinCommand
	{
		return new JoinCommand();
	}

	public function newMatchCommand(): MatchCommand
	{
		return new MatchCommand($this->variables);
	}
	
	public function newMathCommand(): MathCommand
	{
		/*
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
		*/

		$mathjaxUrl = 'https://cdn.jsdelivr.net/npm/mathjax@2/MathJax.js?config=TeX-AMS_CHTML';

		return new MathCommand($this->page, $mathjaxUrl, $this->html);
	}

	public function newMathLineCommand(): MathLineCommand
	{
		/*
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
		*/

		$mathjaxUrl = 'https://cdn.jsdelivr.net/npm/mathjax@2/MathJax.js?config=TeX-AMS_CHTML';

		return new MathLineCommand($this->page, $mathjaxUrl, $this->html);
	}
	
	public function newMenuCommand(): MenuCommand
	{
		return new MenuCommand($this->url, $this->html);
	}

	public function newMenuUpCommand(): MenuUpCommand
	{
		return new MenuUpCommand($this->url, $this->data, $this->interpreter, $this->html);
	}

	public function newMime(): Mime
	{
		return new Mime();
	}
	
	public function newNotCommand(): NotCommand
	{
		return new NotCommand();
	}
	
	public function newOrCommand(): OrCommand
	{
		return new OrCommand();
	}

	public function newPage(): Page
	{
		return new Page();
	}
	
	public function newPageCommand(): PageCommand
	{
		return new PageCommand($this->data, $this->interpreter, $this->page);
	}

	public function newParser(): Parser
	{
		return new Parser();
	}
	
	public function newPathCommand(): PathCommand
	{
		$page = $this->settings['url']['page'];

		return new PathCommand($page);
	}

	public function newReadCommand(): ReadCommand
	{
		return new ReadCommand($this->data, $this->interpreter);
	}

	public function newRedirectCommand(): RedirectCommand
	{
		return new RedirectCommand();
	}

	public function newSchemeCommand(): SchemeCommand
	{
		$scheme = $this->settings['url']['scheme'];

		return new SchemeCommand($scheme);
	}

	public function newSessions(): Sessions
	{
		return new Sessions($this->data);
	}

	public function newSetCommand(): SetCommand
	{
		return new SetCommand($this->variables);
	}

	public function newSiteCommand(): SiteCommand
	{
		$site = $this->settings['url']['site'];

		return new SiteCommand($site);
	}

	public function newStringCommand(): StringCommand
	{
		return new StringCommand();
	}
	
	public function newTitleCommand(): TitleCommand
	{
		return new TitleCommand($this->page);
	}

	public function newTokenAuthenticateCommand(): TokenAuthenticateCommand
	{
		return new TokenAuthenticateCommand($this->tokenAuthenticator, $this->variables);
	}

	public function newTokenAuthenticator(): TokenAuthenticator
	{
		return new TokenAuthenticator($this->tokens, $this->sessions, $this->cookies, $this->url);
	}

	public function newTokens(): Tokens
	{
		return new Tokens($this->data);
	}

	public function newUrl(): Url
	{
		$site = $this->settings['url']['site'];
		$page = $this->settings['url']['page'];

		return new Url($site, $page);
	}

	public function newUrlCommand(): UrlCommand
	{
		return new UrlCommand($this->url);
	}

	public function newUsers(): Users
	{
		return new Users($this->data);
	}

	public function newVariables(): Variables
	{
		return new Variables();
	}
}
