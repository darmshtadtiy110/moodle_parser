<?php


namespace Resources;



use General\Signal;
use Parser\Parser;
use Parser\QuizParser;

class Quiz extends Resource
{
	private $session_key;

	private $cmid;

	private $timer_exist = false;

	private $attempt_list = [];

	/**
	 * @return string
	 */
	public function getSessionKey()
	{
		return $this->session_key;
	}

	/**
	 * @return string
	 */
	public function getCmid()
	{
		return $this->cmid;
	}

	/**
	 * @return boolean
	 */
	public function getTimerExist()
	{
		return $this->timer_exist;
	}

	/**
	 * @return array
	 */
	public function getAttemptList()
	{
		return $this->attempt_list;
	}
	/**
	 * @return ProcessingAttempt
	 */
	public function newAttempt()
	{
		return $this->resource_manager->makeNewAttempt($this);
	}

	public function getAttempt($id)
	{
		return $this->resource_manager->getResource($this->child_array[$id]);
	}

	public function parserLoader(Parser $parser)
	{
		if( $parser instanceof QuizParser )
		{
			Signal::msg("Quiz parser started!");

			$session_key = $parser->getSessionKey();
			$quiz_id = $parser->getQuizId();
			$timer_exist = $parser->getTimer();

			$attempt_list = $parser->getAttemptList();

			$this->setSessionKey($session_key);
			$this->setCmid($quiz_id);
			$this->setTimerExist($timer_exist);

			foreach ($attempt_list as $attempt_array)
			{
				$attempt = new Attempt();

				$attempt->loadFromArray($attempt_array);

				$this->addAttempt($attempt);
			}
		}
	}

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

	public function addAttempt(Attempt $attempt)
	{
		$this->attempt_list = $attempt;
	}
}