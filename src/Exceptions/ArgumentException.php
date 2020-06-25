<?php

namespace Synerga\Exceptions;

use InvalidArgumentException;

class ArgumentException extends InvalidArgumentException implements SynergaExceptionInterface
{
	const TYPE_NULL = 'null';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_INTEGER = 'integer';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';

	private $i;
	private $value;
	private $expectation;

	public function __construct(int $i, $value, string $expectation)
	{
		$this->i = $i;
		$this->value = $value;
		$this->expectation = $expectation;

		$message = $this->newMessage();

		parent::__construct($message);
	}

	public function getData(): array
	{
		return [
			'i' => $this->i,
			'value' => $this->value,
			'expected' => $this->expectation
		];
	}

	public function newMessage(): string
	{
		$valueQuoted = var_export($this->value, true);

		return "Expected {$this->expectation} at position {$this->i}, but received {$valueQuoted}";
	}
}
