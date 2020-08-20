<?php


namespace AttemptProcessor;


use Resources\Question;

class RandomProcessor implements Processor
{
	public function choiceVariant(Question $question)
	{
		//TODO What if one variant (aka text field)
		$answers = $question->getVariants();

		return $answers[mt_rand(0, count($answers) - 1 )];
	}
}