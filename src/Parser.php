<?php

namespace Synerga;

class Parser
{
	/** @var string */
	private $input;

	public function __construct($input)
	{
		$this->input = $input;
	}

	public function getText()
	{
		return $this->input;
	}

	public function getCommand(&$text, &$name, &$arguments)
	{
		// TODO: handle backtracking
		return $this->getCommandBegin($text) &&
			$this->getCommandName($name) &&
			$this->getCommandArguments($arguments) &&
			$this->getCommandEnd();
	}

	private function getCommandBegin(&$output)
	{
		if ($this->scan('(.*?)<:', $matches)) {
			$output = $matches[1];
			return true;
		}

		return false;
	}

	private function getCommandName(&$output)
	{
		if ($this->scan('[a-z_]+', $matches)) {
			$output = $matches[0];
			return true;
		}

		return false;
	}

	private function getCommandArguments(&$arguments)
	{
		$arguments = array();

		while ($this->getWhitespace()) {
			if (!$this->getString($arguments[])) {
				return false;
			}
		}

		return true;
	}

	private function getWhitespace()
	{
		return $this->scan('\\s+');
	}

	private function getString(&$output)
	{
		$expression = '\\"(?:[^"\\x00-\\x1f\\\\]|\\\\(?:["\\\\/bfnrt]|u[0-9a-f]{4}))*\\"';

		if ($this->scan($expression, $matches)) {
			$output = json_decode($matches[0], true);
			return true;
		}

		return false;
	}

	private function getCommandEnd()
	{
		return $this->scan(':>');
	}

	private function scan($expression, &$output = null)
	{
		$delimiter = "\x03";
		$flags = 'As'; // A: anchored // s: the '.' character matches newline

		$pattern = "{$delimiter}{$expression}{$delimiter}{$flags}";

		if (preg_match($pattern, $this->input, $matches) !== 1) {
			return false;
		}

		$output = $matches;
		$length = strlen($matches[0]);
		$this->input = (string)substr($this->input, $length);

		return true;
	}
}
