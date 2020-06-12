<?php


namespace Resources;


use Parser\Parser;

class Attempt extends Resource
{
	/** @var array */
	protected $question_list = [];

	/** @var integer */
	protected $grade;

	public function getGrade()
	{
		return $this->grade;
	}

	public function parserLoader(Parser $parser)
	{
		// TODO: Implement parserLoader() method.
	}
}