<?php


namespace MoodleParser\Processors;


use MoodleParser\Resources\Question;
use MoodleParser\Resources\Variant;

interface QuestionProcessor
{
	/**
	 * @param Question $question
	 * @return Variant
	 */
	public function choiceVariant(Question $question);
}