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

class SynergaSettings
{
	public static function getSettings()
	{
		return [
			'command:controller' => new Instance(['url', 'evaluator'], 'Synerga\\Commands\\ControllerCommand'),
			'command:cookieAuthenticate' => new Instance(['cookieAuthenticator', 'variables'], 'Synerga\\Commands\\CookieAuthenticateCommand'),
			'command:date' => new Instance([], 'Synerga\\Commands\\DateCommand'),
			'command:exists' => new Instance(['data'], 'Synerga\\Commands\\ExistsCommand'),
			'command:file' => new Instance(['file'], 'Synerga\\Commands\\FileCommand'),
			'command:formAuthenticate' => new Instance(['formAuthenticator', 'variables'], 'Synerga\\Commands\\FormAuthenticateCommand'),
			'command:get' => new Instance(['variables'], 'Synerga\\Commands\\GetCommand'),
			'command:head' => new Instance(['page'], 'Synerga\\Commands\\HeadCommand'),
			'command:html' => new Instance(['page'], 'Synerga\\Commands\\HtmlCommand'),
			'command:http' => new Instance([], 'Synerga\\Commands\\HttpCommand'),
			'command:if' => new Instance([], 'Synerga\\Commands\\IfCommand'),
			'command:include' => new Instance(['data', 'interpreter'], 'Synerga\\Commands\\IncludeCommand'),
			'command:integer' => new Instance([], 'Synerga\\Commands\\IntegerCommand'),
			'command:join' => new Instance([], 'Synerga\\Commands\\JoinCommand'),
			'command:math' => new Instance(['page'], 'Synerga\\Commands\\MathCommand'),
			'command:math-line' => new Instance(['page'], 'Synerga\\Commands\\MathLineCommand'),
			'command:menu' => new Instance(['url'], 'Synerga\\Commands\\MenuCommand'),
			'command:not' => new Instance([], 'Synerga\\Commands\\NotCommand'),
			'command:or' => new Instance([], 'Synerga\\Commands\\OrCommand'),
			'command:page' => new Instance(['data', 'interpreter', 'page'], 'Synerga\\Commands\\PageCommand'),
			'command:path' => new Instance(['url'], 'Synerga\\Commands\\PathCommand'),
			'command:set' => new Instance(['variables'], 'Synerga\\Commands\\SetCommand'),
			'command:title' => new Instance(['page'], 'Synerga\\Commands\\TitleCommand'),
			'command:url' => new Instance(['url'], 'Synerga\\Commands\\UrlCommand'),
			'cookieAuthenticator' => new Instance(['sessions', 'cookies'], 'Synerga\\Authenticators\\CookieAuthenticator'),
			'cookies' => new Instance(['cookies:options'], 'Synerga\\Cookies'),
			'cookies:options' => null,
			'data' => new Instance(['data:path'], 'Synerga\\Data'),
			'evaluator' => new Instance(['values'], 'Synerga\\Evaluator'),
			'file' => new Instance(['data', 'mime'], 'Synerga\\File'),
			'formAuthenticator' => new Instance(['users', 'sessions', 'cookies'], 'Synerga\\Authenticators\\FormAuthenticator'),
			'interpreter' => new Instance(['parser', 'evaluator'], 'Synerga\\Interpreter'),
			'mime' => new Instance(['data'], 'Synerga\\Mime'),
			'page' => new Instance([], 'Synerga\\Page'),
			'parser' => new Instance([], 'Synerga\\Parser'),
			'sessions' => new Instance(['data'], 'Synerga\\Sessions'),
			'url' => new Instance(['url:base', 'url:path'], 'Synerga\\Url'),
			'users' => new Instance(['data'], 'Synerga\\Users'),
			'variables' => new Instance([], 'Synerga\\Variables')
		];
	}
}
