<?php


namespace General;


use Request\Request;
use Resources\ProcessingAttempt;

class AttemptProcessor
{
	/**
	 * Randomly select answers for questions in current attempt
	 * @param ProcessingAttempt $attempt
	 * @return bool|Request
	 */
	public static function Random(ProcessingAttempt $attempt)
	{
		$answers = $attempt->getCurrentQuestion()->getVariants();

		$answers_quantity = count($answers);
		$random_variant = rand(0, $answers_quantity - 1 );

		$selected_answer = $answers[$random_variant];

		$post_fields = $attempt->getFormInputs();
		$post_fields[$selected_answer["input_name"]] = $selected_answer["input_value"];

		return Request::ProcessAttempt($post_fields);
	}
}