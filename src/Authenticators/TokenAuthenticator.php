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

namespace Synerga\Authenticators;

use Synerga\Cookies;
use Synerga\Sessions;
use Synerga\Tokens;
use Synerga\Url;

class TokenAuthenticator implements Authenticator
{
	private $tokens;
	private $sessions;
	private $cookies;
	private $url;

	public function __construct(Tokens $tokens, Sessions $sessions, Cookies $cookies, Url $url)
	{
		$this->tokens = $tokens;
		$this->sessions = $sessions;
		$this->cookies = $cookies;
		$this->url = $url;
	}

	public function authenticate(string &$user = null): bool
	{
		// $this->tokens->set('tori', 'tori', false);

		if (!isset($_GET['token'])) {
			return false;
		}

		$key = $_GET['token'];

		if (!$this->tokens->get($key, $user, $isSingleUse)) {
			return false;
		}

		if ($isSingleUse) {
			$this->tokens->unset($key);
		}

		$this->rememberUser($user);

		$url = $this->url->getUrl('', true);

		unset($_GET['token']);

		if (0 < count($_GET)) {
			$queryString = http_build_query($_GET, null, '&', PHP_QUERY_RFC3986);
			$url = "{$url}?{$queryString}";
		}

		header("Location: {$url}");
		exit();
	}

	private function rememberUser($user)
	{
		$sessionId = $this->sessions->set($user);

		// TODO:
		$sessionLife = 691200; // 8 days
		$this->cookies->set('session', $sessionId, $sessionLife);
	}
}
