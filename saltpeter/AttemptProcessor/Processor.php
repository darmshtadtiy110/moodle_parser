<?php


namespace AttemptProcessor;


use Resources\Question;
use Resources\Variant;

interface Processor
{
	/**
	 * @param Question $question
	 * @return Variant
	 */
	public function choiceVariant(Question $question);
}