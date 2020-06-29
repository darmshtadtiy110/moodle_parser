<?php


namespace Resources;


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
}