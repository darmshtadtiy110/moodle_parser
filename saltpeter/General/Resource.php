<?php


namespace General;


use Exception;

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
	 * @throws Exception
	 */
	public function __construct($id, $name = "")
	{
		if(is_int($id))
		{
			$this->id = $id;
		}
		else throw new Exception("Resource id is wrong! ( ".$id." )");

		if($name != "")
		{
			$this->name = $name;
		}
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