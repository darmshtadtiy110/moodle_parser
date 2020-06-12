<?php


namespace Resources;


use Parser\Parser;
use Parser\CourseParser;

class Course extends Resource
{
	private $quiz_list = [];

	private $documents = [];

	/**
	 * @return array
	 */
	public function getQuizList()
	{
		return $this->quiz_list;
	}

	public function getQuiz($id)
	{
		return $this->quiz_list[$id];
	}

	public function parserLoader(Parser $parser)
	{
		if( $parser instanceof CourseParser )
		{
			foreach ( $parser->getQuizList() as $quiz_array)
			{
				$quiz = new Quiz();
				$quiz->loadFromArray($quiz_array);

				$this->addQuiz($quiz);
			}
		}
	}

	/**
	 * @param Document $document
	 */
	public function addDocument(Document $document)
	{
		$this->documents[$document->id()] = $document;
	}

	/**
	 * @param Quiz $quiz
	 */
	public function addQuiz(Quiz $quiz)
	{
		$this->quiz_list[$quiz->id()] = $quiz;
	}


}