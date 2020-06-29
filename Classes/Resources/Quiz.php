<?php


namespace Resources;


class Quiz extends Resource
{
	use ParentResource;

	private $finished_attempt_list = [];

	/** @var ProcessingAttempt */
	private $processing_attempt;

	protected function use_parser()
	{
		$attempt_list = $this->parser()->getAttemptList();

		foreach ($attempt_list as $attempt_array)
		{
			if($attempt_array["finished"] == true)
			{
				$attempt = new FinishedAttempt();

				$attempt->loadFromArray($attempt_array);

				$this->setChild($attempt);
			}
			else {
				$this->processing_attempt = new ProcessingAttempt();

				$this->processing_attempt->loadFromArray($attempt_array);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getAttemptList()
	{
		return $this->finished_attempt_list;
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
		if( !$this->processing_attempt instanceof ProcessingAttempt )
			$this->processing_attempt = new ProcessingAttempt();

		$this->processing_attempt->setSessionKey( $this->parser()->getSessionKey() );

		$this->processing_attempt->setCmid( $this->parser()->getQuizId() );

		$this->processing_attempt->setTimerExist( $this->parser()->getTimer() );

		$this->processing_attempt->parse();

		return $this->processing_attempt;
	}

	public function processingAttempt()
	{
		return $this->processing_attempt;
	}
}