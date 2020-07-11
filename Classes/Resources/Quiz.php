<?php


namespace Resources;

use General\Resource;
use Parser\Resources\QuizParser;


class Quiz extends Resource
{
	private $attempt_list = [];

	private $session_key;

	/** @var bool */
	private $timer_exist;

	public function __construct($id)
	{
		$this->parser = new QuizParser();
		parent::__construct($id);
	}

	public function getAttempt($id)
	{
		if(array_key_exists($id, $this->attempt_list))
			return $this->attempt_list[$id];
		return false;
	}

	public function setAttempt(Attempt $attempt)
	{
		$this->attempt_list[$attempt->getId()] = $attempt;
	}

	public function getTimerExist()
	{
		return $this->timer_exist;
	}

	public function getSessionKey()
	{
		return $this->session_key;
	}

	protected function use_parser()
	{
		$this->session_key = $this->parser->getSessionKey();
		$this->timer_exist = $this->parser->getTimer();
		$this->attempt_list = $this->parser->getAttemptList();
	}

	public function getBestAttempt()
	{
		//TODO
	}

	/**
	 * @return ProcessingAttempt
	 */
	public function startProcessingAttempt()
	{
		if(
			array_key_exists(0, $this->attempt_list) &&
			$this->attempt_list[0] instanceof ProcessingAttempt
		){
			$this->attempt_list[0]->parse();
			return $this->attempt_list[0];
		}
		else {
			return Attempt::Start($this);
		}
	}
}