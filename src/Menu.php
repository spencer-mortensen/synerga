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

class Menu
{
	/** @var Data */
	private $data;

	/** @var Url */
	private $url;

	public function __construct(Data $data, Url $url)
	{
		$this->data = $data;
		$this->url = $url;
	}

	public function getHtml($menuPath)
	{
		$menu = $this->getMenu($menuPath);
		$urlPath = $this->url->getPath();

		$ulInnerHtml = '';

		foreach ($menu as $itemPath => $itemName) {
			$liAttributes = array();
			$aAttributes = array();

			if ($urlPath === $itemPath) {
				$liAttributes['class'] = 'here';
			} else {
				$aAttributes['href'] = $this->url->getUrl($itemPath);
			}

			$textHtml = Html5::getTextHtml($itemName);
			$aHtml = Html5::getElementHtml('a', $aAttributes, $textHtml);
			$ulInnerHtml .= Html5::getElementHtml('li', $liAttributes, $aHtml);
		}

		return Html5::getElementHtml('ul', array(), $ulInnerHtml);
	}

	private function getMenu($path)
	{
		$contents = $this->data->read($path);
		$menu = json_decode($contents, true);

		if (!is_array($menu)) {
			return array();
		}

		return $menu;
	}
}
