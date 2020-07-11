<?php


namespace Resources;

use General\Resource;

abstract class Attempt extends Resource
{
	/**
	 * @param Quiz $quiz
	 * @return ProcessingAttempt
	 */
	public static function Start(Quiz $quiz)
	{
		$new_attempt = new ProcessingAttempt(
			0,
			$quiz->getSessionKey(),
			$quiz->getId(),
			$quiz->getTimerExist()
		);
		$new_attempt->parse();
		return $new_attempt;
	}
}