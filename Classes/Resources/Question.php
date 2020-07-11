<?php


namespace Resources;


use General\Resource;
use Parser\Resources\QuestionParser;

class Question extends Resource
{
	protected $answer;

	/** @var bool */
	protected $state;

	protected $variants = [];

	/** @var integer */
	protected $grade;

	/** @var bool */
	protected $current = false;

	protected $saved = false;

	public function __construct($id, $text)
	{
		$this->parser = new QuestionParser();
		parent::__construct($id, $text);
	}

	protected function use_parser()
	{
		// 1. identity question block
		$this->parser->identityQuestionBlock($this);

		// 2. parse question text
		$this->name = $this->parser->getQuestionText();

		// 3. parse variants
		$this->variants = $this->parser->getVariants();

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
		return $this->name;
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
		return $this->id;
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