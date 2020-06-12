<?php


namespace Resources;


abstract class Question
{
	/** @var String */
	protected $id;

	/** @var String */
	protected $text;

	/** @var String */
	protected $answer;

	/** @var bool */
	protected $state;

	/** @var array */
	protected $variants;

	/** @var integer */
	protected $grade;

	public function __construct($text, $variants, $answer = null, $grade = null)
	{
		$this->text = $text;
		$this->variants = $variants;
		$this->answer = $answer;
		$this->grade = $grade;
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
	 * @param array $variants
	 */
	public function setVariants($variants)
	{
		$this->variants = $variants;
	}


}