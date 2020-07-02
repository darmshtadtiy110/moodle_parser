<?php


namespace Resources;


use General\AttemptProcessor;
use Parser\Resources\ProcessingAttemptParser;
use Request\Request;
use Resources\Questions\Question;

class ProcessingAttempt extends Attempt
{
	/** @var ProcessingAttemptParser */
	protected $parser;

	/** @var string */
	private $session_key;

	/** @var int */
	private $cmid;

	/** @var bool */
	private $timer_exist = false;

	/** @var Question */
	private $current_question;

	/** @var array */
	private $question_list = [];

	private $form_inputs = [];

	/**
	 * @param mixed $session_key
	 */
	public function setSessionKey($session_key)
	{
		$this->session_key = $session_key;
	}

	/**
	 * @param mixed $cmid
	 */
	public function setCmid($cmid)
	{
		$this->cmid = $cmid;
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

	protected function request_resource()
	{
		$this->last_request = Request::StartAttempt(
			$this->session_key,
			$this->cmid,
			$this->timer_exist
		);
	}

	protected function use_parser()
	{
		$questions_on_current_page = $this->parser->getQuestions();
		$this->question_list = array_merge($this->question_list, $questions_on_current_page);
		$this->current_question = $questions_on_current_page[0];

		$this->form_inputs = $this->parser->parseInputs();
	}

	/**
	 * @return Question | false
	 */
	public function getCurrentQuestion()
	{
		return $this->current_question;
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
		// TODO process questions by them quantity
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