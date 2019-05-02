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

class Users
{
	/** @var Data */
	private $data;

	/** @var string */
	private $path;

	public function __construct(Data $data)
	{
		$this->data = $data;
		$this->path = '.config/users';
	}

	public function isUser(string $user): bool
	{
		$users = $this->getUsers();
		return isset($users[$user]);
	}

	public function isUserPassword(string $user, string $password): bool
	{
		$users = $this->getUsers();
		$hash = $users[$user] ?? '';
		return password_verify($password, $hash);
	}

	public function createUser($user, $password)
	{
		$users = $this->getUsers();
		$users[$user] = $this->getPasswordHash($password);
		$this->setUsers($users);
	}

	private function getPasswordHash($password)
	{
		$algorithm = PASSWORD_BCRYPT;

		$options = [
			'cost' => 6
		];

		return password_hash($password, $algorithm, $options);
	}

	public function deleteUser($user)
	{
		$users = $this->getUsers();
		unset($users[$user]);
		$this->setUsers($users);
	}

	public function changePassword($user, $oldPassword, $newPassword)
	{
		// TODO:
		$this->createUser($user, $newPassword);
	}

	// TODO: this is duplicated in the "Sessions" class:
	private function getUsers()
	{
		$json = $this->data->read($this->path);
		$users = json_decode($json, true);

		if ($users === null) {
			return $users = [];
		}

		return $users;
	}

	// TODO: this is duplicated in the "Sessions" class:
	private function setUsers(array $users)
	{
		$json = json_encode($users);
		$this->data->write($this->path, $json);
	}
}
