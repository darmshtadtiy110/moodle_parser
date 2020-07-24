<?php


namespace General;


use \Exception;

abstract class Resource
{
	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	protected $parser;

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

	/**
	 * @return mixed
	 */
	public function parser()
	{
		return $this->parser;
	}

	protected function requestResourcePage()
	{
		$resource_type = Tools::get_object_class_name($this);

		if( is_callable( "Properties::".$resource_type, true ) )
		{
			$link = Properties::$resource_type().$this->getId();

			$resource_request = new Request($link);
			$this->parser()->setParsePage($resource_request->response());

		}
	}

	public function parse()
	{
		$this->requestResourcePage();
		$this->use_parser();

		return $this;
	}

	protected function use_parser() {}
}