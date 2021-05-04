<?php


namespace MoodleParser\Parser\Exceptions;


use Exception;
use MoodleParser\Parser\Parser;
use Throwable;

class ExpressionNotFound extends Exception
{
	/**
	 * ExpressionNotFound constructor.
	 * @param Parser $parser
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct(Parser $parser, $message = "", $code = 0, Throwable $previous = null)
	{
		$alert = $parser->find("div.alert");

		if($message === "" && !empty($alert) )
			$message = $alert[0]->text();

		parent::__construct($message, $code, $previous);
	}
}