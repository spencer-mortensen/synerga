<?php

namespace Synerga\ErrorHandling;

use ErrorException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SpencerMortensen\Exceptions\ErrorHandlerInterface;
use Synerga\ErrorHandling\Exceptions\EvaluationException;
use Synerga\ErrorHandling\Exceptions\FileException;
use Synerga\ErrorHandling\Exceptions\ParserException;
use Synerga\ErrorHandling\Pages\GenericErrorPage;
use Synerga\ErrorHandling\Pages\CodePage;
use Synerga\Html;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
	private $logger;
	private $display;
	private $html;

	public function __construct(LoggerInterface $logger, bool $display, Html $html)
	{
		$this->logger = $logger;
		$this->display = $display;
		$this->html = $html;
	}

	public function handleThrowable(Throwable $throwable)
	{
		$this->logThrowable($throwable);
		$this->showThrowable($throwable);
	}

	private function showThrowable(Throwable $throwable)
	{
		if (!$this->display) {
			$this->showVagueError($throwable);
		}

		if ($throwable instanceof FileException) {
			$path = $throwable->getPath();
			$throwable = $throwable->getException();
		} else {
			$path = null;
		}

		if ($throwable instanceof EvaluationException) {
			$this->showEvaluationError($path, $throwable);
		}

		$this->showGenericError($throwable);
	}

	private function showVagueError(Throwable $throwable)
	{
		$message = 'Please check the error log for more information.';
		$context = null;

		$page = new GenericErrorPage($this->html);
		$pageHtml = $page->getHtml($message, $context);

		$http = new Http500();
		$http->send($pageHtml);
	}

	private function showEvaluationError($path, EvaluationException $exception)
	{
		$title = 'Evaluation Error';
		$titleHtml = $this->html->encode($title);
		$bodyHtml = "<h1>{$titleHtml}</h1>";

		if ($path !== null) {
			$pathHtml = $this->html->encode($path);
			$bodyHtml .= "<p>in “{$pathHtml}”:</p>";
		}

		$text = $exception->getText();
		$position = $exception->getPosition();
		$child = $exception->getThrowable();
		$expectation = $this->getMessage($child);

		$page = new CodePage($this->html);
		$pageHtml = $page->getHtml($title, $bodyHtml, $text, $position, $expectation);

		$http = new Http500();
		$http->send($pageHtml);
	}

	private function getMessage(Throwable $throwable): string
	{
		return $throwable->getMessage();
	}

	private function showGenericError(Throwable $throwable)
	{
		$message = $throwable->getMessage();
		$context = [
			'file' => $throwable->getFile(),
			'line' => $throwable->getLine()
		];

		$page = new GenericErrorPage($this->html);
		$pageHtml = $page->getHtml($message, $context);

		$http = new Http500();
		$http->send($pageHtml);
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
}
