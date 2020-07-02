<?php


namespace Resources;


class Course extends Resource
{
	use ParentResource, Parsable;

	private $quiz_list = [];

	private $document_list = [];

	protected function use_parser()
	{
		foreach ( $this->parser()->getQuizList() as $quiz)
		{

			$this->setChild($quiz);
		}
	}

}