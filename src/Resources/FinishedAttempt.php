<?php


namespace MoodleParser\Resources;


use DiDom\Document;

class FinishedAttempt extends Attempt
{
	/** @var int */
	private $grade;

	/** @var Question[] */
	private $questions = [];


	public function __construct(Document $attempt_review_document)
	{
		$this->parser($attempt_review_document);

		$this->grade = $this->parser()->getGrade();
		$this->questions = $this->parser()->getQuestionsArray();

		parent::__construct($this->parser()->getFinishedAttemptId(), $this->parser()->getName());
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