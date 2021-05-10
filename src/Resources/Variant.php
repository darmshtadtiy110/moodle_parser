<?php


namespace MoodleParser\Resources;


use Exception;

abstract class Variant
{
	/** @var int */
	protected $id;

	/** @var mixed */
	protected $value;

	/** @var bool */
	protected $checked;

	protected $correct = false;

	protected $input_name;

	protected $input_value;

	/**
	 * Variant constructor.
	 * @param $id
	 * @param $checked
	 * @param $input_name
	 * @param $input_value
	 * @throws Exception
	 */
	public function __construct($id, $checked, $input_name, $input_value)
	{
		if( !is_bool($checked) ) throw new Exception("Checked param isn't bool");

		$this->id = (int) $id;
		$this->checked = $checked;
		$this->input_name = $input_name;
		$this->input_value = $input_value;
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function getInputName()
	{
		return $this->input_name;
	}

	/**
	 * @return mixed
	 */
	public function getInputValue()
	{
		return $this->input_value;
	}

	/**
	 * @return bool
	 */
	public function isChecked()
	{
		return $this->checked;
	}

	/**
	 * @return mixed
	 */
	public function isCorrect()
	{
		return $this->correct;
	}

	public function setIsCorrect($correct)
	{
		if(is_bool($correct))
			$this->correct = $correct;
	}
}
