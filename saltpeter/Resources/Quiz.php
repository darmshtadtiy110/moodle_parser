<?php


namespace Resources;


use Exception;
use General\Request;
use General\Resource;
use General\Signal;
use Parser\Resources\ProcessingAttemptParser;

class Quiz extends Resource
{
	private $attempt_list = [];

	private $session_key;

	/** @var bool */
	private $timer_exist;

	public function __construct(
		$id,
		$name,
		$attempt_list,
		$session_key,
		$timer_exist
	) {
		$this->attempt_list = $attempt_list;
		$this->session_key = $session_key;
		$this->timer_exist = $timer_exist;

		parent::__construct($id, $name);
	}

	public function getAttempt($id)
	{
		if(array_key_exists($id, $this->attempt_list))
			return $this->attempt_list[$id];
		return false;
	}

	public function getAttemptList()
	{
		return $this->attempt_list;
	}

	public function getTimerExist()
	{
		return $this->timer_exist;
	}

	public function getSessionKey()
	{
		return $this->session_key;
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
		$start_attempt_request = Request::StartAttempt(
			$this->session_key,
			$this->id,
			$this->timer_exist
		);
		$attempt_parser = new ProcessingAttemptParser();

		$attempt_parser->setParsePage($start_attempt_request->response());

		$new_attempt = false;

		try {
			$new_attempt = new ProcessingAttempt(
				$attempt_parser->getAttemptId(),
				$this->getSessionKey(),
				$this->getId(),
				$this->getTimerExist(),
				$attempt_parser
			);
		}
		catch (Exception $e) { Signal::msg($e->getMessage()); }

		return $new_attempt;
	}
}