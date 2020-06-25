<?php

namespace Synerga\Exceptions;

use Exception;

class CallException extends Exception implements SynergaExceptionInterface
{
	private $class;
	private $argumentException;

	public function __construct(string $class, ArgumentException $argumentException)
	{
		$this->class = $class;
		$this->argumentException = $argumentException;

		$message = $this->newMessage();

		parent::__construct($message);
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function getArgumentException(): ArgumentException
	{
		return $this->argumentException;
	}

	public function getData(): array
	{
		return [
			'class' => $this->class,
			'argument' => $this->argumentException->getData()
		];
	}

	public function newMessage(): string
	{
		$class = $this->getClassName($this->class);
		$message = $this->argumentException->getMessage();

		return "{$class}: {$message}";
	}

	private function getClassName(string $class): string
	{
		$prefix = 'Synerga\\Commands\\';
		$prefixLength = strlen($prefix);

		if (strncmp($class, $prefix, $prefixLength) === 0) {
			return substr($class, $prefixLength);
		}

		return $class;
	}
}
