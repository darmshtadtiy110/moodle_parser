<?php


namespace Resources;


use General\Request;
use General\AttemptProcessor;
use Parser\Resources\ProcessingAttemptParser;

class ProcessingAttempt extends Attempt
{
	/** @var ProcessingAttemptParser */
	protected $parser;

	/** @var string */
	private $session_key;

	/** @var int */
	private $quiz_id;

	/** @var bool */
	private $timer_exist = false;

	/** @var Question[] */
	private $question_list = [];

	private $form_inputs = [];

	public function __construct($id, $session_key, $quiz_id, $timer_exist)
	{
		$this->session_key = $session_key;
		$this->quiz_id = $quiz_id;
		$this->timer_exist = $timer_exist;

		$this->parser = new ProcessingAttemptParser();

		parent::__construct($id);
	}


	/**
	 * @param bool $timer_exist
	 */
	public function setTimerExist($timer_exist)
	{
		$this->timer_exist = $timer_exist;
	}

	/**
	 * @return array
	 */
	public function getQuestionList()
	{
		return $this->question_list;
	}

	/**
	 * @return array
	 */
	public function getFormInputs()
	{
		return $this->form_inputs;
	}

	protected function requestResourcePage()
	{
		$start_attempt_request = Request::StartAttempt(
			$this->session_key,
			$this->quiz_id,
			$this->timer_exist
		);
		$this->parser()->setParsePage($start_attempt_request->response());
	}

	protected function use_parser()
	{
		// 1. get list status of all questions
		// 2. parse those of which on current page
		// 3. parse hidden inputs on page

		$this->updateQuestionList();
		$this->parseCurrentQuestions();

		$this->form_inputs = $this->parser->parseInputs();
	}

	private function updateQuestionList()
	{
		$question_status_array = $this->parser->getQuestionsStatus();

		foreach ($question_status_array as $number => $question_array)
		{
			$question = $this->getQuestion($number);

			if(!$question)
			{
				$new_question = new Question($number);
				$new_question->setSaved($question_array["saved"]);
				$new_question->setCurrent($question_array["current"]);

				$this->setQuestion($new_question);
			}
			else {
				$question->setSaved($question_array["saved"]);
				$question->setCurrent($question_array["current"]);
			}
		}
	}

	private function parseCurrentQuestions()
	{
		foreach ($this->question_list as $question)
		{
			if($question->isCurrent())
			{
				$question->parser()->setParsePage($this->parser->getParsePage());
				$question->parse();
			}
		}
	}

	/**
	 * @param int $number
	 * @return Question|bool
	 */
	public function getQuestion($number)
	{
		if(array_key_exists($number, $this->question_list))
			return $this->question_list[$number];
		else return false;
	}

	/**
	 * @param Question $question
	 */
	public function setQuestion(Question $question)
	{
		if(!array_key_exists($question->getNumber(), $this->question_list))
			$this->question_list[$question->getNumber()] = $question;
	}

	public function getCurrentQuestions()
	{
		$current = [];
		foreach ($this->question_list as $question)
		{
			if($question->isCurrent()) $current[] = $question;
		}
		return $current;
	}

	public function process()
	{
		// TODO Replace processors to questions
		// TODO Processor must work with separate questions
		// TODO process questions by them quantity on attempt
		/**
		 * while () { $this->processor() }
		 * in processor() realize following code
		 */

		// TODO Select processor
		$selected_answer = AttemptProcessor::Random($this);

		$post_fields = $this->getFormInputs();
		$post_fields[$selected_answer["input_name"]] = $selected_answer["input_value"];

		$next_page_request = Request::ProcessAttempt($post_fields);
		$this->parser->setParsePage($next_page_request->response());
		$this->use_parser();
	}
}