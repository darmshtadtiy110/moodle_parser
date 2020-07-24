<?php


namespace Resources;


use Exception;

class FinishedAttempt extends Attempt
{
	/** @var int */
	private $grade;

	/** @var Question[] */
	private $questions = [];

	/**
	 * FinishedAttempt constructor.
	 * @param $id
	 * @param $grade
	 * @param $name
	 * @param array $questions
	 * @throws Exception
	 */
	public function __construct($id, $grade, $name, array $questions)
	{
		if($name == "") throw new Exception("Attempt name is wrong! ( ".$name." )");

		if(!is_int($grade)) throw new Exception(" Attemp grade is wrong! ( ".$grade." )");
		if(empty($questions)) throw new Exception("Questions array is empty!");

		$this->grade = $grade;
		$this->questions = $questions;

		parent::__construct($id, $name);
	}

	/**
	 * @return mixed
	 */
	public function getGrade()
	{
		return $this->grade;
	}

}