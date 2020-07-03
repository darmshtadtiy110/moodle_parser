<?php


namespace Resources\Questions;


use Parser\Resources\Questions\QuestionParser;
use Resources\Parsable;

class Question
{
	use Parsable;

	/** @var int */
	protected $id;

	/** @var int */
	protected $number;

	/** @var String */
	protected $text;

	/** @var Variant */
	protected $answer;

	/** @var bool */
	protected $state;

	/** @var Variant[] */
	protected $variants = [];

	/** @var integer */
	protected $grade;

	/** @var bool */
	protected $current = false;

	protected $saved = false;

	/** @var QuestionParser */
	protected $parser;

	public function __construct($number)
	{
		$this->number = $number;
		$this->setParser();
	}

	protected function use_parser()
	{
		// 1. identity question block
		// 2. parse question text
		// 3. parse variants

		$this->parser->identityQuestionBlock($this);
		$this->text = $this->parser->getQuestionText();

	}

	public function parse()
	{
		$this->use_parser();
		$this->parser()->purgePage();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return String
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @return String
	 */
	public function getAnswer()
	{
		return $this->answer;
	}

	/**
	 * @return bool
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @return array
	 */
	public function getVariants()
	{
		return $this->variants;
	}

	/**
	 * @return int
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @return bool
	 */
	public function isCurrent()
	{
		return $this->current;
	}

	/**
	 * @param bool $current
	 */
	public function setCurrent($current)
	{
		$this->current = $current;
	}

	/**
	 * @return bool
	 */
	public function isSaved()
	{
		return $this->saved;
	}

	/**
	 * @param bool $saved
	 */
	public function setSaved($saved)
	{
		$this->saved = $saved;
	}

	/**
	 * @param array $variant
	 */
	public function setVariant(array $variant)
	{
		if(
			array_key_exists('value', $variant) &&
			array_key_exists('input_name', $variant) &&
			array_key_exists('input_value', $variant)
		) {
			$this->variants[] = [
				"value" => $variant["value"],
				"input_name" => $variant["input_name"],
				"input_value" => $variant["input_value"]
			];
		}
	}
}