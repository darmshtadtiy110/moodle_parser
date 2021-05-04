<?php


namespace MoodleParser\Resources;


use DiDom\Document;
use MoodleParser\Parser\Resources\AttemptParser;

class FinishedAttempt extends Attempt
{
	/** @var int */
	private $grade;

	/** @var Question[] */
	private $questions = [];


	public function __construct(Document $attempt_review_document)
	{
		$attempt_parser = new AttemptParser($attempt_review_document);

		$this->grade = $attempt_parser->getGrade();
		$this->questions = $attempt_parser->getQuestionsArray();

		parent::__construct($attempt_parser->getFinishedAttemptId(), $attempt_parser->getName());
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