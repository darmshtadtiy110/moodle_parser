<?php


namespace Resources;

use General\Resource;
use Interfaces\ParentResource;

use Traits\ParentUtilities;

class Course extends Resource implements ParentResource
{
	use ParentUtilities;

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