<?php


namespace Resources;

use General\Tools;
use General\Signal;
use FileSystem\Cookies;
use Parser\Parser;
use Request\Request;
use Parser\LoginNeededException;
use Request\CurlErrorException;

abstract class Resource
{
	/** @var int */
	protected $id;

	/** @var string */
	protected $name;

	/** @var string */
	protected $link;

	public function id()
	{
		return $this->id;
	}

	public function name()
	{
		return $this->name;
	}

	public function link()
	{
		return $this->link;
	}

	public function loadFromDB()
	{
		return false;
	}

	public function loadFromParser(Cookies $cookies = null)
	{
		try {
			// do request

			$request = new Request($this->link, $cookies);

			// launch parser
			$resource_class = Tools::get_class_name(get_class($this));

			$parser_class = "\\Parser\\".$resource_class."Parser";

			/**
			 * @var Parser $parser
			 * @throws LoginNeededException
			 */
			$parser = new $parser_class($request->response());

			$this->parserLoader($parser);

		}
		catch (CurlErrorException $e) {
			Signal::msg($e->getMessage());
		}
		catch (LoginNeededException $e) {
			//TODO re auth current user
			Signal::msg($e->getMessage());
		}


		return $this;
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

	abstract function parserLoader(Parser $parser);
}