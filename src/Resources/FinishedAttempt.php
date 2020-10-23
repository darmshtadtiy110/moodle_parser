<?php


namespace MoodleParser\Resources;


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
	 */
	public function __construct($id, $grade, $name, array $questions)
	{
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

	public function getQuestions()
	{
		return $this->questions;
	}

}