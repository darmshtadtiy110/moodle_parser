<?php


namespace AttemptProcessor;


use Resources\Question;

class RandomProcessor implements Processor
{
	public function choiceVariant(Question $question)
	{
		//TODO What if one variant (aka text field)
		$answers = $question->getVariants();

		$answers_quantity = count($answers);
		$random_variant = rand(0, $answers_quantity - 1 );

		return $answers[$random_variant];
	}
}