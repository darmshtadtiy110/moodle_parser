<?php


namespace Resources\Questions;


//use Resources\Parsable;

abstract class Question
{
	//use Parsable;

	/** @var String */
	protected $id;

	/** @var String */
	protected $text;

	/** @var String */
	protected $answer;

	/** @var bool */
	protected $state;

	/** @var array */
	protected $variants = [];

	/** @var integer */
	protected $grade;

	public function __construct($question_text)
	{
		$this->text = $question_text;
	}

	/**
	 * @return String
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
	public function isState()
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