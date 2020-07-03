<?php


namespace Resources;


//use Parser\Resources\FinishedAttemptParser;

class FinishedAttempt extends Attempt
{
	/** @var int */
	private $grade;

	/**
	 * @return mixed
	 */
	public function getGrade()
	{
		return $this->grade;
	}

	protected function use_parser()
	{
		// TODO: Implement use_parser() method.
	}

}