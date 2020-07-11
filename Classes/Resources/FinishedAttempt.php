<?php


namespace Resources;


use Parser\Resources\FinishedAttemptParser;

class FinishedAttempt extends Attempt
{
	/** @var int */
	private $grade;

	public function __construct($id, $grade, $name = "")
	{
		$this->grade = $grade;
		$this->parser = new FinishedAttemptParser();
		parent::__construct($id, $name);
	}

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