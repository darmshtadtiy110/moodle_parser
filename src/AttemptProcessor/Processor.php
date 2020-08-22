<?php


namespace MoodleParser\AttemptProcessor;


use MoodleParser\Resources\Question;
use MoodleParser\Resources\Variant;

interface Processor
{
	/**
	 * @param Question $question
	 * @return Variant
	 */
	public function choiceVariant(Question $question);
}