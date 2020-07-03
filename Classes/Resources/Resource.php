<?php


namespace Resources;


use Parser\Parser;

abstract class Resource
{
	use Parsable;

	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	/** @var string */
	protected $link;

	public function __construct()
	{
		$this->setParser();
	}

	public function loadFromDB()
	{
		return false;
	}

	public function loadFromArray($param_array)
	{
		if(array_key_exists("link", $param_array))
			$param_array["id"] = Parser::parseExpressionFromLink("id", $param_array["link"]);

		$class_parameters = get_class_vars(get_class($this));

		foreach ($param_array as $param => $value)
		{
			if( array_key_exists($param, $class_parameters) )
			{
				$this->{$param} = $value;
			}
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
}