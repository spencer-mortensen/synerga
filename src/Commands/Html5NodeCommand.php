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

namespace Synerga\Commands;

use Synerga\Arguments;
use Synerga\Html5;

class Html5NodeCommand implements Command
{
	public function run(Arguments $arguments)
	{
		$name = $arguments->getOptionalString(0);
		$attributes = $arguments->getOptionalArray(1);
		$innerHtml = $arguments->getOptionalString(2);

		$attributesHtml = $this->getAttributesHtml($attributes);

		return "<{$name}{$attributesHtml}>{$innerHtml}</{$name}>";
	}

	private function getAttributesHtml(array $attributes = null): string
	{
		if ($attributes === null) {
			return '';
		}

		$html = '';

		foreach ($attributes as $name => $value) {
			if ($value === null) {
				continue;
			}

			$valueHtml = Html5::getText($value);

			$html .= " {$name}=\"{$valueHtml}\"";
		}

		return $html;
	}
}
