<?php


namespace MoodleParser\Resources;


abstract class Resource
{
	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

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
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}