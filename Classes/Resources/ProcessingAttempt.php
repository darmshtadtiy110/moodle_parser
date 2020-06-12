<?php


namespace Resources;


use DiDom\Document;
use Factory\QuestionFactory;

class ProcessingAttempt extends Attempt
{
	/** @var Document*/
	private $current_page;

	private $questions_chunk = [];

	/** @var integer
	 *  Counter of total questions
	 */
	private $total_questions;

	function __construct($page, $name)
	{
		parent::__construct($page, $name);
		$current_question = QuestionFactory::CreateFromAttempt($this);
	}

	/**
	 * @return Question | false
	 */
	public function getCurrentQuestion()
	{
		if($this->current_question instanceof Question)
			return $this->current_question;

		return false;
	}

	/**
	 * @param Question $current_question
	 */
	public function setCurrentQuestion(Question $current_question)
	{
		$this->current_question = $current_question;
	}

	public function process()
	{
		return true;
	}
}