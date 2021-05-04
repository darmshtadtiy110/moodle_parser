<?php


namespace MoodleParser\Processors;


use MoodleParser\Resources\Question;

class RandomQuestionProcessor implements QuestionProcessor
{
	public function choiceVariant(Question $question)
	{
		//TODO What if one variant (aka text field)
		$answers = $question->getVariants();

		return $answers[mt_rand(0, count($answers) - 1 )];
	}
}