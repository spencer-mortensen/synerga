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

class Sessions
{
	/** @var Data */
	private $data;

	/** @var string */
	private $path;

	public function __construct(Data $data)
	{
		$this->data = $data;
		$this->path = '.config/sessions';
	}

	public function set(string $user)
	{
		$sessions = $this->getSessions();

		$this->getUserId($sessions, $user, $id) ||
		$this->setUserId($sessions, $user, $id);

		return $id;
	}

	private function getUserId(array $sessions, string $user, &$id)
	{
		foreach ($sessions as $sessionId => $sessionUser) {
			if ($sessionUser === $user) {
				$id = $sessionId;
				return true;
			}
		}

		return false;
	}

	private function setUserId(array &$sessions, string $user, &$id)
	{
		do {
			$id = mt_rand();
		} while (isset($sessions[$id]));

		$sessions[$id] = $user;
		$this->setSessions($sessions);

		return true;
	}

	public function unset($id)
	{
		$sessions = $this->getSessions();

		unset($sessions[$id]);

		$this->setSessions($sessions);
	}

	public function get($id, &$user = null)
	{
		$sessions = $this->getSessions();

		if (!isset($sessions[$id])) {
			return false;
		}

		$user = $sessions[$id];
		return true;
	}

	private function getSessions()
	{
		$json = $this->data->read($this->path);
		$sessions = json_decode($json, true);

		if (!is_array($sessions)) {
			$sessions = [];
		}

		return $sessions;
	}

	private function setSessions(array $sessions)
	{
		$json = json_encode($sessions);
		$this->data->write($this->path, $json);
	}
}
