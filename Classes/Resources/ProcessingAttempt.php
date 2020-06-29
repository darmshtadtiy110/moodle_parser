<?php


namespace Resources;


use Parser\AttemptParser;
use Request\CurlErrorException;
use Request\StartAttempt;

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

	protected function setParser()
	{
		$this->parser = new AttemptParser();
	}

	protected function use_parser()
	{

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