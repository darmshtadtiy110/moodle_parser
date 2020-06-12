<?php


namespace ResourceManagers;

use Resources\FinishedAttempt;
use Resources\Quiz;
use Request\StartAttempt;

class AttemptManager
{
	public function getResource($resource_array)
	{
		// TODO: Implement getResource() method.
		return $this->createFromParser($resource_array);
	}

	protected function createFromParser($resource_array)
	{
		// TODO: Implement createFromParser() method.
		return new FinishedAttempt();
	}

	public function makeNewAttempt(Quiz $quiz)
	{
		$new_attempt_request = new StartAttempt(
			$this->session_cookies,
			$quiz->getSessionKey(),
			$quiz->getCmid(),
			$quiz->getTimerExist()
		);

		echo $new_attempt_request->response();
	}
}