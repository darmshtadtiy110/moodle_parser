<?php


namespace MoodleParser\Resources;


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

	public function getBestAttemptID()
	{
		$index = [];

		foreach ($this->attempt_list as $key => $attempt_arr)
		{
			if($attempt_arr["state"] == "finished")
				$index[$attempt_arr["grade"]] = $key;
		}

		return array_pop($index);
	}
}