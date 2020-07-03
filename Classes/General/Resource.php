<?php


namespace General;

use Parser\Parser;

use Interfaces\Parsable;

use Traits\ParserUtilities;

abstract class Resource implements Parsable
{
	use ParserUtilities;

	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	public function loadFromDB()
	{
		return false;
	}

	public function loadFromArray($param_array)
	{
		if(array_key_exists("link", $param_array))
		{
			$param_array["id"] = Parser::parseExpressionFromLink("id", $param_array["link"]);
			unset($param_array["link"]);
		}

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