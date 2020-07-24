<?php


namespace Resources;


abstract class Variant
{
	/** @var int */
	protected $id;

	/** @var mixed */
	protected $value;

	protected $input_name;

	protected $input_value;

	public function __construct($id, $input_name, $input_value)
	{
		$this->id = (int) $id;
		$this->input_name = $input_name;
		$this->input_value = $input_value;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
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
}
