<?php


namespace MoodleParser\Resources;


use DiDom\Document;
use DiDom\Element;
use MoodleParser\Parser\Resources\AttemptParser;
use MoodleParser\Parser\Resources\CourseParser;
use MoodleParser\Parser\Resources\QuestionParser;
use MoodleParser\Parser\Resources\QuizParser;
use MoodleParser\Parser\Resources\StudentParser;
use ReflectionClass;
use ReflectionException;

abstract class Resource
{
	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	/** @var AttemptParser|CourseParser|QuestionParser|QuizParser|StudentParser */
	protected $parser;

	/**
	 * Resource constructor.
	 * @param $id
	 * @param string $name
	 */
	public function __construct($id, $name = "")
	{
		$this->id = $id;

		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * @param Document|Element|null $new_document
	 * @return AttemptParser|CourseParser|QuestionParser|QuizParser|StudentParser
	 */
	public function parser($new_document = null)
	{
		if(!is_null($new_document))
		{
			try {
				$reflection = new ReflectionClass(get_class($this));
				$parser_class_name = "MoodleParser\\Parser\\Resources\\".$reflection->getShortName()."Parser";
			}
			catch (ReflectionException $e) {
				echo "Parser class does not exist: ".$e->getMessage();
			}
			if(isset($parser_class_name))
				$this->parser = new $parser_class_name($new_document);
		}
		return $this->parser;
	}
}