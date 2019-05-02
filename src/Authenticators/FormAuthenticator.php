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
use Synerga\Users;

class FormAuthenticator implements Authenticator
{
	private $users;
	private $sessions;
	private $cookies;

	public function __construct(Users $users, Sessions $sessions, Cookies $cookies)
	{
		$this->users = $users;
		$this->sessions = $sessions;
		$this->cookies = $cookies;
	}

	public function authenticate(string &$user = null): bool
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return false;
		}

		$user = $_POST['authenticate']['user'] ?? null;
		$pass = $_POST['authenticate']['pass'] ?? null;

		if (!isset($user, $pass)) {
			return false;
		}

		// TODO: separate the "Register" and "Sign In" behaviors
		if (!$this->users->isUser($user)) {
			$this->users->createUser($user, $pass);
			$this->rememberUser($user);
			return true;
		}

		if ($this->users->isUserPassword($user, $pass)) {
			$this->rememberUser($user);
			return true;
		}

		return false;
	}

	private function rememberUser($user)
	{
		$sessionId = $this->sessions->set($user);

		// TODO:
		$sessionLife = 691200; // 8 days
		$this->cookies->set('session', $sessionId, $sessionLife);
	}
}
