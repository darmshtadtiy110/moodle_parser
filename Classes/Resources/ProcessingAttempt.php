<?php


namespace Resources;


use Request\CurlErrorException;
use Request\StartAttempt;

use Resources\Question\Question;

class ProcessingAttempt extends Attempt
{
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

	protected function request_resource()
	{
		try {
			$this->last_request = new StartAttempt(
				$this->session_key,
				$this->cmid,
				$this->timer_exist
			);
		}
		catch (CurlErrorException $e) {}
	}

	protected function use_parser()
	{
		$questions_on_current_page = $this->parser()->getQuestions();

		$this->question_list = array_merge($this->question_list, $questions_on_current_page);

		$this->current_question = $questions_on_current_page[0];
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
		return true;
	}
}