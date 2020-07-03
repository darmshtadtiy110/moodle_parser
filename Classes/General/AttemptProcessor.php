<?php


namespace General;


use Resources\FinishedAttempt;
use Resources\ProcessingAttempt;

class AttemptProcessor
{
	/**
	 * Randomly select answers for questions in current attempt
	 * @param ProcessingAttempt $attempt
	 * @return array
	 */
	public static function Random(ProcessingAttempt $attempt)
	{
		$answers = $attempt->getCurrentQuestion()->getVariants();

		$answers_quantity = count($answers);
		$random_variant = rand(0, $answers_quantity - 1 );

		return $answers[$random_variant];
	}

	public static function WithTips(FinishedAttempt $tip, ProcessingAttempt $attempt)
	{
		//TODO Comparing
	}
}