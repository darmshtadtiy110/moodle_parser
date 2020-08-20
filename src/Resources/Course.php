<?php


namespace Resources;

use General\Resource;
use Parser\Resources\CourseParser;

class Course extends Resource
{
	private $quiz_list = [];

	private $document_list = [];

	/** @var CourseParser */
	private $parser;

	public function __construct($id, $name, $quiz_list)
	{
		$this->quiz_list = $quiz_list;

		$this->parser = new CourseParser();

		parent::__construct($id, $name);
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
		$this->quiz_list[$quiz->getId()] = $quiz;
	}

	public function setDocument(Document $doc)
	{
		$this->document_list[$doc->getId()] = $doc;
	}

	protected function use_parser()
	{
		$this->quiz_list = $this->parser->getQuizList();
	}

}