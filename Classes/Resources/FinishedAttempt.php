<?php


namespace Resources;


use Parser\AttemptParser;

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

	protected function setParser()
	{
		$this->parser = new AttemptParser();
	}

	protected function use_parser()
	{
		// TODO: Implement use_parser() method.
	}

}