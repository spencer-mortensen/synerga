<?php

namespace Synerga\ErrorHandling;

use ErrorException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SpencerMortensen\Exceptions\ErrorHandlerInterface;
use Synerga\Exceptions\SynergaExceptionInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
	private $logger;
	private $display;

	public function __construct(LoggerInterface $logger, bool $display)
	{
		$this->logger = $logger;
		$this->display = $display;
	}

	public function handleThrowable(Throwable $throwable)
	{
		$this->logThrowable($throwable);
		$this->showThrowable($throwable);
	}

	private function logThrowable(Throwable $throwable)
	{
		$severity = $this->getThrowableSeverity($throwable);
		$message = $this->getThrowableMessage($throwable);
		$context = [];

		$this->logger->log($severity, $message, $context);
	}

	private function getThrowableSeverity(Throwable $throwable)
	{
		if (!($throwable instanceof ErrorException)) {
			return LogLevel::ERROR;
		}

		$type = $throwable->getSeverity();

		switch ($type) {
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				return LogLevel::INFO;

			case E_NOTICE:
			case E_USER_NOTICE:
				return LogLevel::NOTICE;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				return LogLevel::WARNING;

			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			default:
				return LogLevel::ERROR;
		}
	}

	private function getThrowableMessage(Throwable $throwable)
	{
		$message = $throwable->getMessage();
		$messageText = var_export($message, true);

		$fileAbsolutePath = $throwable->getFile();
		$fileAbsolutePathText = var_export($fileAbsolutePath, true);
		$line = $throwable->getLine();

		return "{$messageText} in {$fileAbsolutePathText}:{$line}";
	}

	private function showThrowable(Throwable $throwable)
	{
		if ($this->display) {
			$message = $throwable->getMessage();
			$context = [
				'file' => $throwable->getFile(),
				'line' => $throwable->getLine()
			];
		} else {
			$message = 'Please check the error log for more information.';
			$context = null;
		}

		$error = new Error500();
		$error->send($message, $context);
	}
}
