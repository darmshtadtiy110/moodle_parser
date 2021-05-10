<?php


namespace MoodleParser\Resources;

use DiDom\Document;

class Course extends Resource
{
	private $quiz_list = [];

	private $document_list = [];

	public function __construct(Document $course)
	{
		$this->parser($course);

		$this->quiz_list = $this->parser()->getQuizList();

		parent::__construct($this->parser()->courseId(), $this->parser()->getCourseName());
	}

	/**
	 * @return array
	 */
	public function getQuizList()
	{
		return $this->quiz_list;
	}

	/**
	 * @return array
	 */
	public function getDocumentList()
	{
		return $this->document_list;
	}

	/**
	 * @param $id
	 * @return Quiz
	 */
	public function getQuiz($id)
	{
		return $this->quiz_list[$id];
	}

	public function getDocument($id)
	{
		if(array_key_exists($id, $this->document_list))
			return $this->document_list[$id];
		return false;
	}

	public function setQuiz(Quiz $quiz)
	{
		$this->quiz_list[$quiz->id()] = $quiz;
	}
/*
	public function setDocument(Document $doc)
	{
		$this->document_list[$doc->id()] = $doc;
	}
	*/
}