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

class Html5
{
	public static function getElementHtml($name, array $attributes = null, $innerHtml = null)
	{
		$attributesHtml = self::getAttributesHtml($attributes);
		return "<{$name}{$attributesHtml}>{$innerHtml}</{$name}>";
	}

	private static function getAttributesHtml($attributes)
	{
		if (count($attributes) === 0) {
			return '';
		}

		$html = '';

		foreach ($attributes as $name => $value) {
			$valueHtml = self::attributeEncode($value);
			$html .= " {$name}=\"{$valueHtml}\"";
		}

		return $html;
	}

	public static function getTextHtml($text)
	{
		$text = htmlspecialchars($text, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_NOQUOTES, 'UTF-8');
		$text = str_replace("\t", '     ', $text);
		$text = str_replace(' ', '&nbsp;', $text);

		return $text;
	}

	private static function attributeEncode($attribute)
	{
		return htmlspecialchars($attribute, ENT_HTML5 | ENT_COMPAT | ENT_DISALLOWED | ENT_QUOTES, 'UTF-8');
	}
}
