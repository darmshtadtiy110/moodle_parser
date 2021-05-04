<?php


namespace MoodleParser\Resources;


class Quiz extends Resource
{
	private $finished_attempts = [];

	private $session_key;

	/** @var bool */
	private $timer_exist;

	public function __construct(
		$id,
		$name,
		$all_attempts,
		$session_key,
		$timer_exist
	) {

		$this->finished_attempts = $this->getFinishedAttemptList($all_attempts);
		$this->session_key = $session_key;
		$this->timer_exist = $timer_exist;

		parent::__construct($id, $name);
	}

	public function getAttempt($id)
	{
		if(array_key_exists($id, $this->finished_attempts))
			return $this->finished_attempts[$id];
		return false;
	}

	public function getFinishedAttempts()
	{
		return $this->finished_attempts;
	}

	private function getFinishedAttemptList($attempts)
	{
		$finished_attempt = [];
		foreach ($attempts as $key => $attempt)
		{
			if($attempt["state"] == "finished")
			{
				$finished_attempt[$key] = $attempt;
			}
		}
		return $finished_attempt;
	}

	public function getTimerExist()
	{
		return $this->timer_exist;
	}

	public function getSessionKey()
	{
		return $this->session_key;
	}

	public function getBestAttemptID()
	{
		$index = [];

		foreach ($this->finished_attempts as $key => $attempt_arr)
		{
			if($attempt_arr["state"] == "finished")
				$index[$attempt_arr["grade"]] = $key;
		}

		return array_pop($index);
	}
}