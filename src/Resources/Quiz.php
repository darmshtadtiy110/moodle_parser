<?php


namespace MoodleParser\Resources;


use DiDom\Document;

class Quiz extends Resource
{
	private $available = false;

	private $finished_attempts = [];

	private $has_processing_attempt = true;

	private $session_key;

	/** @var bool */
	private $timer_exist;

	public function __construct(Document $quiz)
	{
		$this->parser($quiz);

		$this->finished_attempts = $this->getFinishedAttemptList($this->parser()->getAttemptList());

		$this->session_key = $this->parser()->sessionKey();
		$this->timer_exist = $this->parser()->getTimer();

		parent::__construct($this->parser()->quizId(), $this->parser()->getQuizName());
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
			else {
				$this->has_processing_attempt = true;
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

	public function getBestGrade()
	{
		return $this->finished_attempts[$this->getBestAttemptID()]["grade"];
	}

	/**
	 * @return bool
	 */
	public function isAvailable()
	{
		return $this->available;
	}
}