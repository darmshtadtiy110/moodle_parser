<?php


namespace Resources;

use General\Resource;
use Parser\Resources\CourseParser;

class Course extends Resource
{
	private $quiz_list = [];

	private $document_list = [];

	public function __construct($id)
	{
		$this->parser = new CourseParser();
		parent::__construct($id);
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

	public function getQuiz($id)
	{
		if(array_key_exists($id, $this->quiz_list))
			return $this->quiz_list[$id];
		return false;
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