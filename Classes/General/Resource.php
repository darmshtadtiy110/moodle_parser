<?php


namespace General;

use General\Exceptions\CurlErrorException;

//use \Exception;

abstract class Resource
{
	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	protected $parser;

	public function __construct($id, $name = "")
	{
		if(is_int($id))
		{
			$this->id = $id;
		}
		//else throw new Exception("Resource id isn't integer");

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

	/** @deprecated  */
	protected function setParser()
	{
		$resource_class = get_class($this);
		$parser_class = "\\Parser\\".$resource_class."Parser";
		$this->parser = new $parser_class();
	}

	protected function requestResourcePage()
	{
		$resource_type = Tools::get_object_class_name($this);

		if( is_callable( "Properties::".$resource_type, true ) )
		{
			$link = Properties::$resource_type().$this->getId();

			try {
				$resource_request = new Request($link);
				$this->parser()->setParsePage($resource_request->response());
			}
			catch (CurlErrorException $e) {
				Signal::msg("Curl error in request for ".get_class()." ".$e->getMessage());
			}
		}
	}

	public function parse()
	{
		//$this->setParser();
		$this->requestResourcePage();
		$this->use_parser();
	}

	abstract protected function use_parser();
}